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
        $unit = ChronoUnit::SECONDS();

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
        self::assertFalse(ChronoUnit::MICROS()->isDateBased());
        self::assertFalse(ChronoUnit::MILLIS()->isDateBased());
        self::assertFalse(ChronoUnit::SECONDS()->isDateBased());
        self::assertFalse(ChronoUnit::MINUTES()->isDateBased());
        self::assertFalse(ChronoUnit::HOURS()->isDateBased());
        self::assertFalse(ChronoUnit::HALF_DAYS()->isDateBased());
        self::assertTrue(ChronoUnit::DAYS()->isDateBased());
        self::assertTrue(ChronoUnit::WEEKS()->isDateBased());
        self::assertTrue(ChronoUnit::MONTHS()->isDateBased());
        self::assertTrue(ChronoUnit::YEARS()->isDateBased());
        self::assertTrue(ChronoUnit::DECADES()->isDateBased());
        self::assertTrue(ChronoUnit::CENTURIES()->isDateBased());
        self::assertTrue(ChronoUnit::MILLENNIA()->isDateBased());
        self::assertFalse(ChronoUnit::FOREVER()->isDateBased());
    }

    public function testIsDurationEstimated(): void
    {
        self::assertFalse(ChronoUnit::MICROS()->isDurationEstimated());
        self::assertFalse(ChronoUnit::MILLIS()->isDurationEstimated());
        self::assertFalse(ChronoUnit::SECONDS()->isDurationEstimated());
        self::assertFalse(ChronoUnit::MINUTES()->isDurationEstimated());
        self::assertFalse(ChronoUnit::HOURS()->isDurationEstimated());
        self::assertFalse(ChronoUnit::HALF_DAYS()->isDurationEstimated());
        self::assertTrue(ChronoUnit::DAYS()->isDurationEstimated());
        self::assertTrue(ChronoUnit::WEEKS()->isDurationEstimated());
        self::assertTrue(ChronoUnit::MONTHS()->isDurationEstimated());
        self::assertTrue(ChronoUnit::YEARS()->isDurationEstimated());
        self::assertTrue(ChronoUnit::DECADES()->isDurationEstimated());
        self::assertTrue(ChronoUnit::CENTURIES()->isDurationEstimated());
        self::assertTrue(ChronoUnit::MILLENNIA()->isDurationEstimated());
        self::assertFalse(ChronoUnit::FOREVER()->isDurationEstimated());
    }

    public function testIsTimeBased(): void
    {
        self::assertTrue(ChronoUnit::MICROS()->isTimeBased());
        self::assertTrue(ChronoUnit::MILLIS()->isTimeBased());
        self::assertTrue(ChronoUnit::SECONDS()->isTimeBased());
        self::assertTrue(ChronoUnit::MINUTES()->isTimeBased());
        self::assertTrue(ChronoUnit::HOURS()->isTimeBased());
        self::assertTrue(ChronoUnit::HALF_DAYS()->isTimeBased());
        self::assertFalse(ChronoUnit::DAYS()->isTimeBased());
        self::assertFalse(ChronoUnit::WEEKS()->isTimeBased());
        self::assertFalse(ChronoUnit::MONTHS()->isTimeBased());
        self::assertFalse(ChronoUnit::YEARS()->isTimeBased());
        self::assertFalse(ChronoUnit::DECADES()->isTimeBased());
        self::assertFalse(ChronoUnit::CENTURIES()->isTimeBased());
        self::assertFalse(ChronoUnit::MILLENNIA()->isTimeBased());
        self::assertFalse(ChronoUnit::FOREVER()->isTimeBased());
    }

    public function testValues(): void
    {
        self::assertSame(
            [
                ChronoUnit::MICROS(),
                ChronoUnit::MILLIS(),
                ChronoUnit::SECONDS(),
                ChronoUnit::MINUTES(),
                ChronoUnit::HOURS(),
                ChronoUnit::HALF_DAYS(),
                ChronoUnit::DAYS(),
                ChronoUnit::WEEKS(),
                ChronoUnit::MONTHS(),
                ChronoUnit::YEARS(),
                ChronoUnit::DECADES(),
                ChronoUnit::CENTURIES(),
                ChronoUnit::MILLENNIA(),
                ChronoUnit::FOREVER(),
            ],
            ChronoUnit::values()
        );
    }
}
