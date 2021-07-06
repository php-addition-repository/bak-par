<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Chrono;

use Par\Core\PHPUnit\EnumTestCaseTrait;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoUnit;
use Par\Time\Temporal\Temporal;
use PHPUnit\Framework\TestCase;

class ChronoUnitTest extends TestCase
{
    use HashableAssertions;
    use EnumTestCaseTrait;

    public function testCanDetermineSupportOfTemporal(): void
    {
        $unit = ChronoUnit::Seconds();

        $temporal = $this->createMock(Temporal::class);
        $temporal->method('supportsUnit')->with($unit)->willReturn(true);

        self::assertTrue($unit->isSupportedBy($temporal));
    }

//    public function testGetDuration(): void
//    {
//        self::assertSame(365000, ChronoUnit::MILLENNIA()->getDuration()->toDays());
//        self::assertSame(36500, ChronoUnit::CENTURIES()->getDuration()->toDays());
//        self::assertSame(3650, ChronoUnit::DECADES()->getDuration()->toDays());
//        self::assertSame(365, ChronoUnit::YEARS()->getDuration()->toDays());
//        self::assertSame(30, ChronoUnit::MONTHS()->getDuration()->toDays());
//        self::assertSame(7, ChronoUnit::WEEKS()->getDuration()->toDays());
//        self::assertSame(1, ChronoUnit::DAYS()->getDuration()->toDays());
//
//        self::assertSame(12, ChronoUnit::HALF_DAYS()->getDuration()->toHours());
//        self::assertSame(1, ChronoUnit::HOURS()->getDuration()->toHours());
//        self::assertSame(1, ChronoUnit::MINUTES()->getDuration()->toMinutes());
//        self::assertSame(1, ChronoUnit::SECONDS()->getDuration()->toSeconds());
//        self::assertSame(1, ChronoUnit::MILLIS()->getDuration()->toMillis());
//        self::assertSame(1, ChronoUnit::MICROS()->getDuration()->toMicros());
//    }
//
//    public function testGetDurationOfForeverThrowsException(): void
//    {
//        $this->expectException(UnsupportedTemporalType::class);
//        ChronoUnit::FOREVER()->getDuration();
//    }

    public function testIsDateBased(): void
    {
        self::assertFalse(ChronoUnit::Micros()->isDateBased());
        self::assertFalse(ChronoUnit::Millis()->isDateBased());
        self::assertFalse(ChronoUnit::Seconds()->isDateBased());
        self::assertFalse(ChronoUnit::Minutes()->isDateBased());
        self::assertFalse(ChronoUnit::Hours()->isDateBased());
        self::assertFalse(ChronoUnit::HalfDays()->isDateBased());
        self::assertTrue(ChronoUnit::Days()->isDateBased());
        self::assertTrue(ChronoUnit::Weeks()->isDateBased());
        self::assertTrue(ChronoUnit::Months()->isDateBased());
        self::assertTrue(ChronoUnit::Years()->isDateBased());
        self::assertTrue(ChronoUnit::Decades()->isDateBased());
        self::assertTrue(ChronoUnit::Centuries()->isDateBased());
        self::assertTrue(ChronoUnit::Millennia()->isDateBased());
        self::assertFalse(ChronoUnit::Forever()->isDateBased());
    }

    public function testIsDurationEstimated(): void
    {
        self::assertFalse(ChronoUnit::Micros()->isDurationEstimated());
        self::assertFalse(ChronoUnit::Millis()->isDurationEstimated());
        self::assertFalse(ChronoUnit::Seconds()->isDurationEstimated());
        self::assertFalse(ChronoUnit::Minutes()->isDurationEstimated());
        self::assertFalse(ChronoUnit::Hours()->isDurationEstimated());
        self::assertFalse(ChronoUnit::HalfDays()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Days()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Weeks()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Months()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Years()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Decades()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Centuries()->isDurationEstimated());
        self::assertTrue(ChronoUnit::Millennia()->isDurationEstimated());
        self::assertFalse(ChronoUnit::Forever()->isDurationEstimated());
    }

    public function testIsTimeBased(): void
    {
        self::assertTrue(ChronoUnit::Micros()->isTimeBased());
        self::assertTrue(ChronoUnit::Millis()->isTimeBased());
        self::assertTrue(ChronoUnit::Seconds()->isTimeBased());
        self::assertTrue(ChronoUnit::Minutes()->isTimeBased());
        self::assertTrue(ChronoUnit::Hours()->isTimeBased());
        self::assertTrue(ChronoUnit::HalfDays()->isTimeBased());
        self::assertFalse(ChronoUnit::Days()->isTimeBased());
        self::assertFalse(ChronoUnit::Weeks()->isTimeBased());
        self::assertFalse(ChronoUnit::Months()->isTimeBased());
        self::assertFalse(ChronoUnit::Years()->isTimeBased());
        self::assertFalse(ChronoUnit::Decades()->isTimeBased());
        self::assertFalse(ChronoUnit::Centuries()->isTimeBased());
        self::assertFalse(ChronoUnit::Millennia()->isTimeBased());
        self::assertFalse(ChronoUnit::Forever()->isTimeBased());
    }

    public function testValues(): void
    {
        self::assertSame(
            [
                ChronoUnit::Micros(),
                ChronoUnit::Millis(),
                ChronoUnit::Seconds(),
                ChronoUnit::Minutes(),
                ChronoUnit::Hours(),
                ChronoUnit::HalfDays(),
                ChronoUnit::Days(),
                ChronoUnit::Weeks(),
                ChronoUnit::Months(),
                ChronoUnit::Years(),
                ChronoUnit::Decades(),
                ChronoUnit::Centuries(),
                ChronoUnit::Millennia(),
                ChronoUnit::Forever(),
            ],
            ChronoUnit::values()
        );
    }
}
