<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventSubscriber;

use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class DoubleAuthentificationSubscriber implements EventSubscriberInterface
{
    final public const string ROLE_2FA_SUCCEED = 'ROLE_2FA_SUCCEED';
    final public const string FIREWALL_NAME = 'main';

    public function __construct(private readonly RouterInterface $router, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if ('app_security_authentification_protected' !== $route) {
            return;
        }

        $currentToken = $this->tokenStorage->getToken();
        if (!$currentToken instanceof PostAuthenticationToken) {
            $response = new RedirectResponse($this->router->generate('app_login'));
            $event->setResponse($response);

            return;
        }

        if (!$currentToken->getUser() instanceof UserInterface || self::FIREWALL_NAME !== $currentToken->getFirewallName()) {
            return;
        }

        if ($this->hasRole($currentToken, self::ROLE_2FA_SUCCEED)) {
            return;
        }

        $response = new RedirectResponse($this->router->generate('app_security_setup_fa'));
        $event->setResponse($response);
    }

    private function hasRole(TokenInterface $token, string $role): bool
    {
        return \in_array($role, $token->getRoleNames(), true);
    }
}
