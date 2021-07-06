<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;
use Par\Time\Temporal\TemporalUnit;
use Par\TimeTest\Fixtures\DecoratedTemporal;

final class PlusUnitTest extends AbstractTestCase
{
    public function testItWillAdjustTemporalUsingNativeModifier(): void
    {
        $expectedModifier = 'modify to this';

        $native = $this->createNativeMockWithModificationExpectation($expectedModifier);

        $unit = $this->createMock(TemporalUnit::class);
        $unit->method('toNativeModifier')->willReturn($expectedModifier);

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->willReturn(true);
        $temporalMock->method('toNative')->willReturn($native);

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::plusUnit(1, $unit)->adjustInto($temporal);
        self::assertNotSame($temporal, $result);
    }

    public function testItWillReturnProvidedTemporalWhenNoChangeIsNeeded(): void
    {
        $unit = $this->createMock(TemporalUnit::class);

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->willReturn(true);

        $temporal = new DecoratedTemporal($temporalMock);

        $result = TemporalAdjusters::plusUnit(0, $unit)->adjustInto($temporal);
        self::assertSame($temporal, $result);
    }

    public function testItWillThrowExceptionWhenTemporalDoesNotSupportUnit(): void
    {
        $unit = $this->createMock(TemporalUnit::class);

        $temporalMock = $this->createMock(Temporal::class);
        $temporalMock->method('supportsUnit')->with($unit)->willReturn(false);

        $this->expectException(UnsupportedTemporalType::class);

        TemporalAdjusters::plusUnit(1, $unit)->adjustInto($temporalMock);
    }
}