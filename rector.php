<?php

/*
 * This file is part of SILARHI.
 * (c) 2019 - present Guillaume Sainthillier <guillaume@silarhi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withCache(__DIR__ . '/var/tools/rector')
    ->withImportNames()
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        symfonyCodeQuality: true,
    )
    ->withComposerBased(
        twig: true,
        symfony: true,
    )
    ->withSkip([
        // strict_types would conflict with the php-cs-fixer header layout; opt in explicitly if wanted
        SafeDeclareStrictTypesRector::class,
        // Flex-managed file, keep fully-qualified bundle class names
        __DIR__ . '/config/bundles.php',
    ])
;
