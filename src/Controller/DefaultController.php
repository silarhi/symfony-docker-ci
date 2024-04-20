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

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    public function index(string $projectDir): Response
    {
        return $this->render('default/index.html.twig', [
            'description' => file_get_contents($projectDir . \DIRECTORY_SEPARATOR . 'README.md'),
        ]);
    }
}
