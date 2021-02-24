<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Chrono;

use Par\Core\PHPUnit\EnumTestCaseTrait;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoField;
use Par\Time\Chrono\ChronoUnit;
use Par\Time\Factory;
use Par\Time\Temporal\TemporalAccessor;
use Par\Time\Temporal\ValueRange;
use PHPUnit\Framework\TestCase;

class ChronoFieldTest extends TestCase
{
    use HashableAssertions;
    use EnumTestCaseTrait;

    public function testDayOfMonth(): void
    {
        $field = ChronoField::DayOfMonth();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertHashEquals(ChronoUnit::Days(), $field->getBaseUnit());
        self::assertHashEquals(ChronoUnit::Months(), $field->getRangeUnit());
        self::assertSame((int)$native->format('j'), $field->getFromNative($native));
        self::assertHashEquals(ValueRange::ofVariableMax(1, 28, 31), $field->range());
        self::assertTrue($field->isDateBased());
        self::assertFalse($field->isTimeBased());
    }

    public function testDayOfWeek(): void
    {
        $field = ChronoField::DayOfWeek();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertHashEquals(ChronoUnit::Days(), $field->getBaseUnit());
        self::assertHashEquals(ChronoUnit::Weeks(), $field->getRangeUnit());
        self::assertSame((int)$native->format('N'), $field->getFromNative($native));
        self::assertHashEquals(ValueRange::ofFixed(1, 7), $field->range());
        self::assertTrue($field->isDateBased());
        self::assertFalse($field->isTimeBased());
    }

    public function testIsSupportedBy(): void
    {
        $field = ChronoField::MonthOfYear();

        $expected = true;
        $temporal = $this->createMock(TemporalAccessor::class);
        $temporal->method('supportsField')->with($field)->willReturn($expected);

        self::assertSame($expected, $field->isSupportedBy($temporal));
    }

    public function testMonthOfYear(): void
    {
        $field = ChronoField::MonthOfYear();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertHashEquals(ChronoUnit::Months(), $field->getBaseUnit());
        self::assertHashEquals(ChronoUnit::Years(), $field->getRangeUnit());
        self::assertSame((int)$native->format('n'), $field->getFromNative($native));
        self::assertHashEquals(ValueRange::ofFixed(1, 12), $field->range());
        self::assertTrue($field->isDateBased());
        self::assertFalse($field->isTimeBased());
    }

    public function testDayOfYear(): void
    {
        $field = ChronoField::DayOfYear();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertHashEquals(ChronoUnit::Days(), $field->getBaseUnit());
        self::assertHashEquals(ChronoUnit::Years(), $field->getRangeUnit());
        self::assertSame((int)$native->format('z') + 1, $field->getFromNative($native));
        self::assertHashEquals(ValueRange::ofVariableMax(1, 365, 366), $field->range());
        self::assertTrue($field->isDateBased());
        self::assertFalse($field->isTimeBased());
    }

    public function testValues(): void
    {
        self::assertSame(
            [
                ChronoField::DayOfWeek(),
                ChronoField::DayOfMonth(),
                ChronoField::DayOfYear(),
                ChronoField::MonthOfYear(),
                ChronoField::Year(),
            ],
            ChronoField::values()
        );
    }

    public function testYear(): void
    {
        $field = ChronoField::Year();

        $native = Factory::create(2018, 3, 4, 5, 6, 7);

        self::assertHashEquals(ChronoUnit::Years(), $field->getBaseUnit());
        self::assertHashEquals(ChronoUnit::Forever(), $field->getRangeUnit());
        self::assertSame((int)$native->format('Y'), $field->getFromNative($native));
        self::assertHashEquals(ValueRange::ofFixed(-999999999, 999999999), $field->range());
        self::assertTrue($field->isDateBased());
        self::assertFalse($field->isTimeBased());
    }
}