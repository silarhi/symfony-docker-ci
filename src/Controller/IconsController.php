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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IconsController extends AbstractController
{
    /** The dumped export shown on the page, so the code shown is the code dumped */
    private const string DUMP_EXAMPLE_EXPORT = 'LucideTrash2Icon';

    #[Route(path: '/icons', name: 'icons')]
    public function index(
        #[Autowire('%app.dumped_icons%')]
        string $dumpedIcons,
    ): Response {
        $dump = is_file($dumpedIcons) ? (string) file_get_contents($dumpedIcons) : '';

        return $this->render('icons/index.html.twig', [
            'dumpExample' => 1 === preg_match('/^export const ' . self::DUMP_EXAMPLE_EXPORT . ' = .*$/m', $dump, $matches) ? $matches[0] : null,
        ]);
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
        ]);
    }
}
