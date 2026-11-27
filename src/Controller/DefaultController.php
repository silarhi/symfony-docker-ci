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
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;
use Twig\Extra\Markdown\MarkdownRuntime;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    #[Cache(smaxage: 300, public: true)]
    public function index(string $projectDir, CacheInterface $cache, Environment $twig): Response
    {
        $readme = $projectDir . \DIRECTORY_SEPARATOR . 'README.md';

        // Converted once per version of the file, rather than on every request
        $readmeHtml = $cache->get('readme.' . filemtime($readme), static fn (): string => $twig
            ->getRuntime(MarkdownRuntime::class)
            ->convert((string) file_get_contents($readme)));

        return $this->render('default/index.html.twig', [
            'readme' => $readmeHtml,
        ]);
    }
}
