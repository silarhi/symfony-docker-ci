<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Keeps a page of #[Cache(public: true)] shared by the CDN for a visitor who cannot be anyone, and gives it an ETag:
 * the browser revalidates it and gets a 304 when nothing changed.
 *
 * On a stateful firewall, reading the security token (the navbar's is_granted()) counts as using the session, even
 * when there is none: AbstractSessionListener then turns every page private. A visitor without a session cookie gets
 * the same page as all the others, so this listener tells the session listener to leave its Cache-Control alone.
 * Logged-in visitors always carry the session cookie: the CDN must bypass the requests sending it (same setup as
 * offre-parrainages.fr and by-night.fr).
 */
final readonly class PublicCacheListener
{
    /** Caddy's encode directive suffixes the ETag of a compressed response, and the browser sends it back so */
    private const string ENCODING_SUFFIX = '/-(?:zstd|br|gzip)"/';

    public function __construct(
        #[Autowire(service: 'security.untracked_token_storage')]
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * Before AbstractSessionListener (-1000), which reads the header and removes it.
     */
    #[AsEventListener(priority: -999)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        if (!$event->isMainRequest() || !$this->isShareable($request, $response)) {
            return;
        }

        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $this->revalidate($request, $response);
    }

    private function isShareable(Request $request, Response $response): bool
    {
        if (!$request->isMethodCacheable() || !$response->headers->hasCacheControlDirective('public')) {
            return false;
        }

        // A logged-in visitor, or one in the middle of the login and 2FA steps
        if ($request->hasPreviousSession()) {
            return false;
        }

        // Something to keep for this visitor (a flash, a login target path)
        if ([] !== $response->headers->getCookies()) {
            return false;
        }

        // Logged in on this very request
        if (null !== $this->tokenStorage->getToken()?->getUser()) {
            return false;
        }

        $session = $request->hasSession() ? $request->getSession() : null;

        return !$session instanceof Session || !$session->isStarted() || $session->isEmpty();
    }

    private function revalidate(Request $request, Response $response): void
    {
        $content = $response->getContent();
        if (!$response->isOk() || false === $content) {
            return;
        }

        if (null === $response->getEtag()) {
            $response->setEtag(hash('xxh128', $content));
        }

        $ifNoneMatch = $request->headers->get('If-None-Match');
        if (null !== $ifNoneMatch) {
            $request->headers->set('If-None-Match', preg_replace(self::ENCODING_SUFFIX, '"', $ifNoneMatch));
        }

        $response->isNotModified($request);
    }
}
