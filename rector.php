<?php

declare(strict_types=1);
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withComposerBased(phpunit: true)
    ->withPaths([
        __FILE__,
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        LocallyCalledStaticMethodToNonStaticRector::class,
        PreferPHPUnitThisCallRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withSets([
        LevelSetList::UP_TO_PHP_85,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        PHPUnitSetList::PHPUNIT_MOCK_TO_STUB,
        PHPUnitSetList::PHPUNIT_NARROW_ASSERTS,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])
    ->withImportNames()
    ->withParallel();
