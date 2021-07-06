<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use Par\Time\Chrono\ChronoUnit;
use Par\Time\DayOfWeek;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;
use Par\TimeTest\Fixtures\DecoratedTemporal;

final class PreviousTest extends AbstractTestCase
{
    public function testItWillAdjustToPreviousDayOfWeek(): void
    {
        $adjustTo = DayOfWeek::Thursday();

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->willReturn(true);

        $native = $this->createNativeMockWithModificationExpectation('previous Thursday');
        $temporalMock->expects(self::once())->method('toNative')->with()->willReturn($native);

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::previous($adjustTo)->adjustInto($temporal);
        self::assertNotSame($temporal, $result);
    }

    public function testItWillThrowExceptionWhenTemporalDoesNotSupportDays(): void
    {
        $unit = ChronoUnit::Days();

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->with($unit)->willReturn(false);

        $this->expectException(UnsupportedTemporalType::class);

        $adjustTo = DayOfWeek::Thursday();
        TemporalAdjusters::previous($adjustTo)->adjustInto($temporalMock);
    }
}