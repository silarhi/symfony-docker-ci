<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2021 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class DoubleAuthentificationSuscriber implements EventSubscriberInterface
{
    public const ROLE_2FA_SUCCEED = '2FA_SUCCEED';
    public const FIREWALL_NAME = 'main';

    public function __construct(private RouterInterface $router, private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if (!\in_array($route, ['app_security_authentification_protected'], true)) {
            return;
        }

        $currentToken = $this->tokenStorage->getToken();
        if (!$currentToken instanceof PostAuthenticationGuardToken) {
            $response = new RedirectResponse($this->router->generate('app_login'));
            $event->setResponse($response);

            return;
        }

        if (!$currentToken->isAuthenticated() || self::FIREWALL_NAME !== $currentToken->getProviderKey()) {
            return;
        }

        if ($this->hasRole($currentToken, self::ROLE_2FA_SUCCEED)) {
            return;
        }

        $response = new RedirectResponse($this->router->generate('app_security_validate_authentification'));
        $event->setResponse($response);
    }

    private function hasRole(TokenInterface $token, string $role): bool
    {
        foreach ($token->getRoleNames() as $userRole) {
            if ($userRole === $role) {
                return true;
            }
        }

        return false;
    }
}
