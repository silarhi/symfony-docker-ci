<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2020 Guillaume Sainthillier <hello@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/authentification", name="app_security_authentification")
     */
    public function index(SessionInterface $session)
    {
        if (!$session->has('qrCodeSession')) {
            $google2fa = new Google2FA();

            $secretKey = $google2fa->generateSecretKey();

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                'Silarhi',
                'hello@silarhi.fr',
                $secretKey
            );

            $writer = new Writer(
                new ImageRenderer(
                    new RendererStyle(400),
                    new ImagickImageBackEnd()
                )
            );

            $qrCodeImage = base64_encode($writer->writeString($qrCodeUrl));

            $qrCodeSession = [
                'secretKey' => $secretKey,
                'qrCodeImage' => $qrCodeImage,
            ];

            $session->set('qrCodeSession', $qrCodeSession);
        }

        $getQrCodeSession = $session->get('qrCodeSession');

        return $this->render('security/qrCode.html.twig', [
            'qrCodeImage' => $getQrCodeSession['qrCodeImage'],
            'secretKey' => $getQrCodeSession['secretKey'],
        ]);
    }

    /**
     * @Route("/valide-authentification", name="app_security_validate_authentification")
     */
    public function valideAuthentification(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/valide_authentification.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/2FA-protected", name="app_security_authentification_protected")
     */
    public function authentificationProtected()
    {
        if ($this->getUser()) {
            return $this->render('security/protected.html.twig');
        }

        return $this->redirectToRoute('homepage');
    }
}
