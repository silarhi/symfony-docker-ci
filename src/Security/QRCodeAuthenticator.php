<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2020 Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Security;

use App\EventSubscriber\DoubleAuthentificationSuscriber;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class QRCodeAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_security_validate_authentification';

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session, TokenStorageInterface $tokenStorage, UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return [
            'qrCode' => $request->request->get('qrCode'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        //Get user from login form
        $existingToken = $this->tokenStorage->getToken();
        if (null === $existingToken) {
            return null;
        }

        return $existingToken->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $qrCode = (string) $credentials['qrCode'];

        if (!$user) {
            return false;
        }

        $google2fa = new Google2FA();
        $google2fa->setSecret($this->session->get('qrCodeSession')['secretKey']);

        if (true !== $google2fa->verifyKey($google2fa->getSecret(), $qrCode)) {
            throw new CustomUserMessageAuthenticationException('This code is not valid');
        }

        return true;
    }

    public function createAuthenticatedToken(UserInterface $user, string $providerKey)
    {
        $currentToken = parent::createAuthenticatedToken($user, $providerKey);

        $roles = array_merge($currentToken->getRoleNames(), [DoubleAuthentificationSuscriber::ROLE_2FA_SUCCEED]);

        return new PostAuthenticationGuardToken(
            $currentToken->getUser(),
            $currentToken->getProviderKey(),
            $roles
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_security_authentification_protected'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
