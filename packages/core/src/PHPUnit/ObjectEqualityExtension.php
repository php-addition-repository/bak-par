<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use PHPUnit\Runner\BeforeFirstTestHook;
use SebastianBergmann\Comparator\Factory;

/**
 * A PHPUnit extension that adds the ability to use the "assertEquals" assertion on when the expected object implements
 * the ObjectEquality interface.
 *
 * Include the following in your phpunit configuration xml to enable the extension:
 * ```xml
 * <phpunit ..>
 *
 *   <extensions>
 *         <extension class="\Par\Core\PHPUnit\ObjectEqualityExtension"/>
 *   </extensions>
 *
 * </phpunit>
 * ```
 */
final class ObjectEqualityExtension implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        Factory::getInstance()->register(
            new ObjectEqualityComparator()
        );
    }

}
