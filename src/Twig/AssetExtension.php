<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Twig;

use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Attribute\AsTwigFunction;

class AssetExtension
{
    public function __construct(private readonly UrlGeneratorInterface $router, private readonly string $secret)
    {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    #[AsTwigFunction(name: 'app_asset', isSafe: ['html'])]
    public function appAsset(string $path, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $parameters['fm'] = 'pjpg';

        if (str_ends_with($path, 'png')) {
            $parameters['fm'] = 'png';
        }

        $parameters['s'] = SignatureFactory::create($this->secret)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->router->generate('asset_url', $parameters, $referenceType);
    }
}
