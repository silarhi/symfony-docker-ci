<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2022 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route(path: '/images', name: 'images')]
    public function index()
    {
        return $this->render('images/index.html.twig');
    }
}
