<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Cache(maxage: 0, smaxage: self::SHARED_MAX_AGE, public: true, staleWhileRevalidate: self::SHARED_MAX_AGE)]
class IconsController extends AbstractController
{
    /** The dumped export shown on the page, so the code shown is the code dumped */
    private const string DUMP_EXAMPLE_EXPORT = 'LucideTrash2Icon';

    #[Route(path: '/icons', name: 'icons')]
    public function index(
        #[Autowire('%app.dumped_icons%')]
        string $dumpedIcons,
        CacheInterface $cache,
    ): Response {
        return $this->render('icons/index.html.twig', [
            'dumpExample' => is_file($dumpedIcons) ? $this->dumpExample($dumpedIcons, $cache) : null,
        ]);
    }

    /**
     * The line of the dump that exports the example icon, read once per version of the dump.
     */
    private function dumpExample(string $dumpedIcons, CacheInterface $cache): ?string
    {
        return $cache->get('icons.dump_example.' . filemtime($dumpedIcons), static function () use ($dumpedIcons): ?string {
            $dump = (string) file_get_contents($dumpedIcons);

            return 1 === preg_match('/^export const ' . self::DUMP_EXAMPLE_EXPORT . ' = .*$/m', $dump, $matches) ? $matches[0] : null;
        });
    }

    /**
     * What an API typically returns: the icon of each item is a name, only known at runtime.
     */
    #[Route(path: '/icons/api/status.json', name: 'icons_api_status')]
    public function apiStatus(): JsonResponse
    {
        return new JsonResponse([
            ['label' => 'Web servers', 'icon' => 'lucide:server', 'status' => 'Operational', 'statusIcon' => 'lucide:circle-check'],
            ['label' => 'Database', 'icon' => 'lucide:database', 'status' => 'Operational', 'statusIcon' => 'lucide:circle-check'],
            ['label' => 'Emails', 'icon' => 'lucide:mail', 'status' => 'Degraded', 'statusIcon' => 'lucide:triangle-alert'],
            ['label' => 'Payments', 'icon' => 'lucide:credit-card', 'status' => 'Down', 'statusIcon' => 'lucide:circle-x'],
            // A name the dump does not know: the fallback stays
            ['label' => 'Legacy API', 'icon' => 'lucide:does-not-exist', 'status' => 'Unknown', 'statusIcon' => null],
        ], headers: ['X-Robots-Tag' => 'noindex']);
    }
}
