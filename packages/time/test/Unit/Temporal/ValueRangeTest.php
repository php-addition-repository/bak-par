<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal;

use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\ValueRange;
use PHPUnit\Framework\TestCase;

final class ValueRangeTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateRangeWithFixedValues(): void
    {
        $min = 0;
        $max = 10;
        $range = ValueRange::ofFixed($min, $max);

        self::assertSame($min, $range->getMinimum());
        self::assertSame($max, $range->getMaximum());
    }

    /**
     * @test
     */
    public function itWillThrowInvalidArgumentExceptionWithFixedWhenMaxIsLessThanMin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofFixed(1, 0);
    }

    /**
     * @test
     */
    public function itCanCreateRangeWithVariableMax(): void
    {
        $min = 0;
        $smallestMax = 28;
        $largestMax = 31;
        $range = ValueRange::ofVariableMax($min, $smallestMax, $largestMax);

        self::assertSame($min, $range->getMinimum());
        self::assertSame($smallestMax, $range->getSmallestMaximum());
        self::assertSame($largestMax, $range->getMaximum());
    }

    /**
     * @test
     */
    public function itWillThrowInvalidArgumentExceptionWithVariableMaxWhenMaxLessThanMin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofVariableMax(1, 0, 1);
    }

    /**
     * @test
     */
    public function itCanCreateVariable(): void
    {
        $smallestMin = 0;
        $largestMin = 1;
        $smallestMax = 28;
        $largestMax = 31;
        $range = ValueRange::ofVariable($smallestMin, $largestMin, $smallestMax, $largestMax);

        self::assertSame($smallestMin, $range->getMinimum());
        self::assertSame($largestMin, $range->getLargestMinimum());
        self::assertSame($smallestMax, $range->getSmallestMaximum());
        self::assertSame($largestMax, $range->getMaximum());
    }

    public function itWillThrowInvalidArgumentExceptionWithVariableWhenSmallestMinGreaterThanLargestMin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ValueRange::ofVariable(1, 0, 3, 4);
    }

    /**
     * @return array<array-key, array{int, int, int, int, string}>
     */
    public function provideForStringTransforming(): array
    {
        return [
            [0, 1, 2, 3, '0/1 - 2/3'],
            [0, 1, 3, 3, '0/1 - 3'],
            [0, 0, 3, 3, '0 - 3'],
            [0, 0, 2, 3, '0 - 2/3'],
        ];
    }

    /**
     * @dataProvider provideForStringTransforming
     * @test
     *
     * @param int    $smallestMin
     * @param int    $largestMin
     * @param int    $smallestMax
     * @param int    $largestMax
     * @param string $expected
     */
    public function itCanTransformToString(int $smallestMin,
                                           int $largestMin,
                                           int $smallestMax,
                                           int $largestMax,
                                           string $expected): void
    {
        $range = ValueRange::ofVariable($smallestMin, $largestMin, $smallestMax, $largestMax);
        self::assertSame($expected, $range->toString());
    }

    /**
     * @test
     */
    public function itCanDetermineEquality(): void
    {
        $expected = ValueRange::ofVariable(0, 1, 2, 3);

        self::assertTrue($expected->equals(ValueRange::ofVariable(0, 1, 2, 3)));
        self::assertFalse($expected->equals(ValueRange::ofFixed(0, 1)));
        self::assertFalse($expected->equals(null));
    }

    /**
     * @test
     */
    public function itIsHashable(): void
    {
        $range = ValueRange::ofVariable(0, 1, 2, 3);

        self::assertSame('0/1-2/3', $range->hash());
    }

    /**
     * @test
     */
    public function itCanDetermineIfValueIsValid(): void
    {
        $range = ValueRange::ofVariable(0, 1, 2, 3);

        self::assertFalse($range->isValidValue(-1));
        self::assertTrue($range->isValidValue(0));
        self::assertTrue($range->isValidValue(1));
        self::assertTrue($range->isValidValue(2));
        self::assertTrue($range->isValidValue(3));
        self::assertFalse($range->isValidValue(4));
    }

    /**
     * @test
     */
    public function itWillContinueWhenCheckingValidValue(): void
    {
        $range = ValueRange::ofFixed(0, 5);

        $expected = 2;

        $field = $this->createMock(TemporalField::class);

        self::assertSame($expected, $range->checkValidValue($expected, $field));
    }

    /**
     * @test
     */
    public function itWillThrowInvalidArgumentExceptionWhenCheckingInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $range = ValueRange::ofFixed(0, 5);

        $expected = 6;

        $field = $this->createMock(TemporalField::class);
        $field->method('toString')->willReturn('Mocked');

        $range->checkValidValue($expected, $field);
    }
}