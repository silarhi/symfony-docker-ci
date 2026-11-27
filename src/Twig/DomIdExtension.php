<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Twig;

use Override;
use Symfony\Contracts\Service\ResetInterface;
use Twig\Attribute\AsTwigFunction;

/**
 * Ids numbered in the order of the page: the same page renders the same HTML, so its ETag lets the browser revalidate
 * it (App\EventListener\PublicCacheListener), where random() changed it on every request.
 */
class DomIdExtension implements ResetInterface
{
    /** @var array<string, int> */
    private array $counters = [];

    #[AsTwigFunction(name: 'dom_id')]
    public function domId(string $prefix): string
    {
        $this->counters[$prefix] = ($this->counters[$prefix] ?? 0) + 1;

        return $prefix . '-' . $this->counters[$prefix];
    }

    /**
     * Between two requests of a FrankenPHP worker.
     */
    #[Override]
    public function reset(): void
    {
        $this->counters = [];
    }
}
