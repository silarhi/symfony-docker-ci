<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Kernel;

require_once \dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    if ($context['APP_MAINTENANCE'] ?? false) {
        echo '<html><body><h1>Upgrade in progress</h1></body></html>';
        exit(1);
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
