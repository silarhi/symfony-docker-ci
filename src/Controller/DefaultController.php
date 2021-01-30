<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2021 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(string $projectDir, MarkdownParserInterface $parser)
    {
        return $this->render('default/index.html.twig', [
            'description' => $parser->transformMarkdown(file_get_contents($projectDir . \DIRECTORY_SEPARATOR . 'README.md')),
        ]);
    }
}
