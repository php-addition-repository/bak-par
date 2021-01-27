<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;
use Par\TimeTest\Fixtures\DecoratedTemporal;

final class LastDayOfMonthTest extends AbstractTestCase
{
    public function testItWillAdjustToFirstDayOfMonth(): void
    {
        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsField')->willReturn(true);

        $native = $this->createNativeMockWithModificationExpectation('last day of this month');
        $temporalMock->expects(self::once())->method('toNative')->with()->willReturn($native);

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::lastDayOfMonth()->adjustInto($temporal);

        self::assertNotSame($temporal, $result);
    }
}