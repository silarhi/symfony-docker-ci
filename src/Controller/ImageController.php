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
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route(path: '/images', name: 'images')]
    #[Cache(smaxage: 300, public: true)]
    public function index(): Response
    {
        return $this->render('images/index.html.twig');
    }
}
