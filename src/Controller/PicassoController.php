<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Silarhi\PicassoBundle\Service\ImageHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PicassoController extends AbstractController
{
    /** A literal data URI, e.g. a dominant color stored next to the image: a light grey 3:2 SVG */
    private const string SOLID_COLOR_PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 3 2'%3E%3Crect width='3' height='2' fill='%23d6d1cc'/%3E%3C/svg%3E";

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
            'photo' => $photo,
            'placeholders' => $placeholders,
            'solidColorPlaceholder' => self::SOLID_COLOR_PLACEHOLDER,
        ]);
    }
}
