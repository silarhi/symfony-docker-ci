<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2022 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use InvalidArgumentException;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssetsController extends AbstractController
{
    #[Route(path: '/assets/{path<(.+)>}', name: 'asset_url', methods: ['GET'])]
    public function asset(string $path, string $secret, Request $request, Server $glide)
    {
        $parameters = $request->query->all();
        if (\count($parameters) > 0) {
            try {
                SignatureFactory::create($secret)->validateRequest($path, $parameters);
            } catch (SignatureException $e) {
                throw $this->createNotFoundException('', $e);
            }
        }
        $glide->setResponseFactory(new SymfonyResponseFactory($request));
        try {
            $response = $glide->getImageResponse($path, $parameters);
        } catch (InvalidArgumentException|FileNotFoundException $e) {
            throw $this->createNotFoundException('', $e);
        }

        return $response;
    }
}
