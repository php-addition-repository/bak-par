<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\LocalDate;

use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\LocalDate;
use Par\Time\Month;
use Par\Time\Year;
use PHPUnit\Framework\TestCase;

final class AccessorTest extends TestCase
{
    use HashableAssertions;

    public function testItCanRetrieveMonth(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertHashEquals(Month::of(2), $source->month());
    }

    public function testItCanRetrieveMonthPrimitive(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertSame(2, $source->monthValue());
    }

    public function testItCanRetrieveYear(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertHashEquals(Year::of(2011), $source->year());
    }

    public function testItCanRetrieveYearPrimitive(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertSame(2011, $source->yearValue());
    }

    public function testItCanRetrieveDayOfMonth(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertSame(10, $source->dayOfMonth());
    }

    public function testItCanRetrieveDayOfYear(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        self::assertSame(41, $source->dayOfYear());
    }

    public function testItCanRetrieveDayOfYearInLeapYear(): void
    {
        $source = LocalDate::of(2012, 12, 31);

        self::assertSame(366, $source->dayOfYear());
    }
}