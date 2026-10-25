<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SeoController extends AbstractController
{
    /** The public pages, in the order of the menu: the security pages stay out (noindex) */
    private const array SITEMAP_ROUTES = ['homepage', 'images', 'picasso', 'icons'];

    #[Route(path: '/robots.txt', name: 'robots', methods: ['GET'], format: 'txt')]
    public function robots(): Response
    {
        return $this->render('seo/robots.txt.twig');
    }

    #[Route(path: '/sitemap.xml', name: 'sitemap', methods: ['GET'], format: 'xml')]
    public function sitemap(): Response
    {
        return $this->render('seo/sitemap.xml.twig', [
            'routes' => self::SITEMAP_ROUTES,
        ]);
    }
}
