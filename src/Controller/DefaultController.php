<?php

namespace App\Controller;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{name}", name="homepage")
     */
    public function index(string $projectDir, MarkdownParserInterface $parser, ?string $name = null)
    {
        return $this->render('default/index.html.twig', [
            'name' => $name,
            'description' => $parser->transformMarkdown(file_get_contents($projectDir . DIRECTORY_SEPARATOR . 'README.md'))
        ]);
    }
}
