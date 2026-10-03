<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Silarhi\PicassoBundle\Dto\ImageRenderData;
use Silarhi\PicassoBundle\Service\ImageHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PicassoController extends AbstractController
{
    /** A literal data URI, e.g. a dominant color stored next to the image: a light grey 3:2 SVG */
    private const string SOLID_COLOR_PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 3 2'%3E%3Crect width='3' height='2' fill='%23d6d1cc'/%3E%3C/svg%3E";

    /** The image the JSON API example renders */
    private const string API_EXAMPLE_IMAGE = 'build/images/elvira-visser-k89j1SUqf5U-unsplash.jpg';

    #[Route(path: '/picasso', name: 'picasso')]
    public function index(ImageHelperInterface $imageHelper, Packages $packages): Response
    {
        $photo = $packages->getUrl('build/images/diana-parkhouse-1166627-unsplash.jpg');

        // The preview each placeholder strategy shows while the photo loads
        $placeholders = [];
        foreach (['blur' => true, 'blurhash' => 'blurhash', 'placeholderData' => null] as $strategy => $placeholder) {
            $placeholders[$strategy] = $imageHelper->imageData(
                src: $photo,
                width: 600,
                height: 400,
                fit: 'cover',
                placeholder: $placeholder,
                placeholderData: 'placeholderData' === $strategy ? self::SOLID_COLOR_PLACEHOLDER : null,
            )->placeholderUri;
        }

        return $this->render('picasso/index.html.twig', [
            'placeholders' => $placeholders,
            'apiExample' => $this->apiExample($imageHelper, $packages),
        ]);
    }

    /**
     * What a headless frontend (React, Vue, a mobile app…) gets to render the responsive image itself.
     */
    #[Route(path: '/picasso/api/image.json', name: 'picasso_api_image')]
    public function apiImage(ImageHelperInterface $imageHelper, Packages $packages): JsonResponse
    {
        return new JsonResponse($this->apiExample($imageHelper, $packages));
    }

    private function apiExample(ImageHelperInterface $imageHelper, Packages $packages): ImageRenderData
    {
        return $imageHelper->imageData(
            src: $packages->getUrl(self::API_EXAMPLE_IMAGE),
            width: 1200,
            height: 800,
            sizes: '100vw',
            fit: 'cover',
        );
    }
}
