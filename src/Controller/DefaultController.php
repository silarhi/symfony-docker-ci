<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/{name}", name="homepage")
     */
    public function index(?string $name = null)
    {
        return $this->render('default/index.html.twig', [
            'name' => $name,
        ]);
    }
}
