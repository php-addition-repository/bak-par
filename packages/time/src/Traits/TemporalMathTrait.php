<?php

declare(strict_types=1);

namespace Par\Time\Traits;

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
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): static
    {
        return $this->plus($amountToSubtract * -1, $unit);
    }

    /**
     * @inheritDoc
     */
    public function minusAmount(TemporalAmount $amount): static
    {
        return $amount->subtractFrom($this);
    }

    /**
     * @inheritDoc
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): static
    {
        $adjuster = TemporalAdjusters::plusUnit($amountToAdd, $unit);

        return $this->with($adjuster);
    }

    /**
     * @inheritDoc
     */
    public function plusAmount(TemporalAmount $amount): static
    {
        return $amount->addTo($this);
    }
}
