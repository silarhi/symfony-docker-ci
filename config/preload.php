<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - 2022 Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

if (file_exists(\dirname(__DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require \dirname(__DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php';
}
