<?php

declare(strict_types=1);

namespace Par\Time\Traits;

use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjusters;
use Par\Time\Temporal\TemporalAmount;
use Par\Time\Temporal\TemporalUnit;

/**
 * @psalm-require-implements Temporal
 */
trait TemporalMathTrait
{
    /**
     * @inheritDoc
     *
     * @return static
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): Temporal
    {
        return $this->plus($amountToSubtract * -1, $unit);
    }

    /**
     * @inheritDoc
     *
     * @return static
     */
    public function minusAmount(TemporalAmount $amount): Temporal
    {
        return $amount->subtractFrom($this);
    }

    /**
     * @inheritDoc
     *
     * @return static
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): Temporal
    {
        $adjuster = TemporalAdjusters::plusUnit($amountToAdd, $unit);

        return $this->with($adjuster);
    }

    /**
     * @inheritDoc
     *
     * @return static
     */
    public function plusAmount(TemporalAmount $amount): Temporal
    {
        return $amount->addTo($this);
    }
}