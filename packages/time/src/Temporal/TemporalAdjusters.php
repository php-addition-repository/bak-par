<?php

namespace Par\Time\Temporal;

final class TemporalAdjusters
{
    /**
     * @param int          $amountToAdd
     * @param TemporalUnit $unit
     *
     * @return TemporalAdjuster
     *
     * @psalm-mutation-free
     */
    public static function plusUnit(int $amountToAdd, TemporalUnit $unit): TemporalAdjuster
    {
        return new TemporalUnitAdjuster($amountToAdd, $unit);
    }
}