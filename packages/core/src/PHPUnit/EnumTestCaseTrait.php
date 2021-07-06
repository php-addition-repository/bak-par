<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use Par\Core\Enum;
use ReflectionClass;

/**
 * Trait containing a PHPUnit\Framework\TestCase::setUp implementation that will make sure enums work as expected
 * between tests.
 */
trait EnumTestCaseTrait
{
    protected function setUp(): void
    {
        // Reset all static properties
        $reflectionClass = new ReflectionClass(Enum::class);

        $definitionCache = $reflectionClass->getProperty('definitionCache');
        $definitionCache->setAccessible(true);
        $definitionCache->setValue($definitionCache->getDefaultValue());

        $instances = $reflectionClass->getProperty('instances');
        $instances->setAccessible(true);
        $instances->setValue($instances->getDefaultValue());

        $allInstancesLoaded = $reflectionClass->getProperty('allInstancesLoaded');
        $allInstancesLoaded->setAccessible(true);
        $allInstancesLoaded->setValue($allInstancesLoaded->getDefaultValue());
    }
}