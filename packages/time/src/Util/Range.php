<?php

declare(strict_types=1);

namespace Par\Time\Util;

/**
 * @internal
 */
final class Range
{
    /**
     * @param int $current
     * @param int $change
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public static function calculateOverflow(int $current, int $change, int $min, int $max): int
    {
        $newValue = $current + $change;

        if ($newValue === 0) {
            $newValue = $max;
        }

        $rangeMultiplier = (int)floor($newValue / $max);

        if ($newValue < $min) {
            $rangeMultiplier *= -1;
            $newValue = ($rangeMultiplier * $max) + $newValue;
        }

        if ($newValue > $max) {
            $newValue -= $rangeMultiplier * $max;
        }

        return $newValue;
    }
}