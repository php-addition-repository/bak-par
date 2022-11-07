<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\Values;

use Par\Core\ObjectEquality;
use Par\Core\Values;
use PHPUnit\Framework\TestCase;

class EqualsTest extends TestCase
{

    /**
     * @test
     */
    public function itCanDetermineEqualityBetweenObjectEqualityInstanceAndOther(): void
    {
        $b = 'string';

        $a = $this->createMock(ObjectEquality::class);
        $a->expects(self::once())
          ->method('equals')
          ->with($b)
          ->willReturn(false);

        self::assertFalse(Values::equals($a, $b));
    }

    /**
     * @test
     */
    public function itCanDetermineEqualityBetweenOtherAndObjectEqualityInstance(): void
    {
        $b = 'string';

        $a = $this->createMock(ObjectEquality::class);
        $a->expects(self::once())
          ->method('equals')
          ->with($b)
          ->willReturn(false);

        self::assertFalse(Values::equals($b, $a));
    }

    /**
     * @test
     */
    public function itCanDetermineEqualityBetweenValuesNotImplementingObjectEquality(): void
    {
        self::assertTrue(Values::equals('foo', 'foo'));

        self::assertFalse(Values::equals('foo', 'bar'));
        self::assertFalse(Values::equals(null, 'bar'));
        self::assertFalse(Values::equals(1, 1.0));
    }
}
