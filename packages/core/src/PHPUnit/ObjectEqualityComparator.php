<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use Par\Core\ObjectEquality;
use Par\Core\Values;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

final class ObjectEqualityComparator extends Comparator
{
    public function accepts($expected, $actual): bool
    {
        return $expected instanceof ObjectEquality || $actual instanceof ObjectEquality;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     * @param float $delta
     * @param bool  $canonicalize
     * @param bool  $ignoreCase
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        if (!Values::equals($expected, $actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $this->exporter->export($expected),
                $this->exporter->export($actual),
                false,
                'Failed asserting that two objects are equal.'
            );
        }
    }

}
