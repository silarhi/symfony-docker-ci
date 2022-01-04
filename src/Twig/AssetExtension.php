<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2022 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Twig;

use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    public function __construct(private UrlGeneratorInterface $router, private string $secret)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('app_asset', [$this, 'appAsset'], ['is_safe' => ['html']]),
        ];
    }

    public function appAsset(string $path, array $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $parameters['fm'] = 'pjpg';

        if ('png' === substr($path, -3)) {
            $parameters['fm'] = 'png';
        }

        $parameters['s'] = SignatureFactory::create($this->secret)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->router->generate('asset_url', $parameters, $referenceType);
    }
}
