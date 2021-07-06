<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\Temporal\TemporalAdjusters;

use DateTimeImmutable;
use Par\TimeTest\Fixtures\DecoratedTemporal;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        DecoratedTemporal::$decorated = null;
    }

    protected function createNativeMockWithModificationExpectation(string $modification): DateTimeImmutable
    {
        $native = $this->createMock(DateTimeImmutable::class);
        $native->expects(self::once())->method('modify')->with($modification)->willReturn($native);

        return $native;
    }
}