<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use Par\Time\Chrono\ChronoField;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;

final class FirstDayOfMonthTest extends AbstractTestCase
{
    public function testItWillAdjustToFirstDayOfMonth(): void
    {
        $temporalMock = $this->createMock(Temporal::class);

        $expected = $this->createMock(Temporal::class);
        $temporalMock->method('withField')->with(ChronoField::DayOfMonth(), 1)->willReturn($expected);

        $result = TemporalAdjusters::firstDayOfMonth()->adjustInto($temporalMock);

        self::assertSame($expected, $result);
    }
}