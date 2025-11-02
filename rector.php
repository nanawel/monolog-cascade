<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([
        \Rector\Set\ValueObject\SetList::CODE_QUALITY,
        \Rector\Set\ValueObject\SetList::CODING_STYLE,
        \Rector\Set\ValueObject\SetList::TYPE_DECLARATION,
        \Rector\Set\ValueObject\SetList::PHP_74,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_50,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_60,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_70,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_80,
        \Rector\PHPUnit\Set\PHPUnitSetList::PHPUNIT_90,
    ])
;
