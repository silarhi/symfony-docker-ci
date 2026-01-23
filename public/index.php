<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Kernel;
use Symfony\Component\HttpFoundation\Response;

require_once \dirname(__DIR__) . '/vendor/autoload_runtime.php';

return static function (array $context) {
    if ($context['APP_MAINTENANCE'] ?? false) {
        /** @var string $html */
        $html = file_get_contents(__DIR__ . '/../maintenance.html');

        return new Response($html, Response::HTTP_SERVICE_UNAVAILABLE);
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
