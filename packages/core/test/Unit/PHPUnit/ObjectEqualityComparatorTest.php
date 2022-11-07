<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\PHPUnit;

use Par\Core\ObjectEquality;
use Par\Core\PHPUnit\ObjectEqualityComparator;
use Par\CoreTest\Fixtures\ScalarValueObject;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Comparator\ComparisonFailure;

final class ObjectEqualityComparatorTest extends TestCase
{
    /**
     * @return iterable<string, array{ScalarValueObject, mixed, bool}>
     */
    public function provideDataForEvaluate(): iterable
    {
        $expectedValue = 'foo';
        $expected = new ScalarValueObject($expectedValue);
        $other = new ScalarValueObject('bar');

        yield 'same-instances' => [$expected, $expected, true];
        yield 'same-instance-values' => [$expected, new ScalarValueObject($expectedValue), false];
        yield 'different-instance-value' => [$expected, $other, false];
        yield 'different-value-' => [$expected, 'bar', false];
    }

    public function testItAcceptsWhenExpectedOrActualImplementsObjectEqualityInterface(): void
    {
        $comparator = new ObjectEqualityComparator();

        $this->assertTrue($comparator->accepts(new ScalarValueObject('foo'), 'bar'));
        $this->assertTrue($comparator->accepts('bar', new ScalarValueObject('foo')));
        $this->assertFalse($comparator->accepts('bar', 'foo'));
    }

    public function testItAssertsEqualityViaExpectedOrActual(): void
    {
        $comparator = new ObjectEqualityComparator();

        $actual = 'bar';

        $expected = $this->createMock(ObjectEquality::class);
        $expected->expects($this->exactly(2))->method('equals')->with($actual)->willReturn(true);

        $comparator->assertEquals($expected, $actual);
        $comparator->assertEquals($actual, $expected);

        $this->expectException(ComparisonFailure::class);
        $comparator->assertEquals(new ScalarValueObject('foo'), 'bar');
    }
}
