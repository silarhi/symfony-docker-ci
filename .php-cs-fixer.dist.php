<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$header = <<<'EOF'
This file is part of SILARHI.
(c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/public')
    ->in(__DIR__ . '/src')
    ->append([
        __FILE__,
        __DIR__ . '/rector.php',
    ])
;

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => ['header' => $header],
        'concat_space' => ['spacing' => 'one'],
        'native_constant_invocation' => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized']],
        'global_namespace_import' => [
            'import_classes' => true,
        ],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');
