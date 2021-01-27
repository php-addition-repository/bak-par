<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use Par\Time\Chrono\ChronoUnit;
use Par\Time\DayOfWeek;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;
use Par\TimeTest\Fixtures\DecoratedTemporal;

final class NextOrSameTest extends AbstractTestCase
{
    public function testItWillAdjustToNextDayOfWeekIfNotSame(): void
    {
        $current = DayOfWeek::Monday();
        $adjustTo = DayOfWeek::Thursday();

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->willReturn(true);
        $temporalMock->method('get')->willReturn($current->value());

        $native = $this->createNativeMockWithModificationExpectation('next Thursday');
        $temporalMock->expects(self::once())->method('toNative')->willReturn($native);

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::nextOrSame($adjustTo)->adjustInto($temporal);
        self::assertNotSame($temporal, $result);
    }

    public function testItWillNotAdjustToNextDayOfWeekIfSame(): void
    {
        $current = DayOfWeek::Monday();

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->willReturn(true);
        $temporalMock->method('get')->willReturn($current->value());
        $temporalMock->expects(self::never())->method('toNative');

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::nextOrSame($current)->adjustInto($temporal);
        self::assertSame($temporal, $result);
    }

    public function testItWillThrowExceptionWhenTemporalDoesNotSupportDays(): void
    {
        $unit = ChronoUnit::Days();

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->with($unit)->willReturn(false);

        $this->expectException(UnsupportedTemporalType::class);

        $adjustTo = DayOfWeek::Thursday();
        TemporalAdjusters::nextOrSame($adjustTo)->adjustInto($temporalMock);
    }
}