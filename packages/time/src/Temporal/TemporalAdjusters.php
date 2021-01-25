<?php

namespace Par\Time\Temporal;

use Par\Time\Exception\UnsupportedTemporalType;

final class TemporalAdjusters
{
    public static function plusUnit(int $amountToAdd, TemporalUnit $unit): TemporalAdjuster
    {
        /**
         * @psalm-mutation-free
         */
        return new class($amountToAdd, $unit) implements TemporalAdjuster {
            public function __construct(private int $amount, private TemporalUnit $unit)
            {
            }

            public function adjustInto(Temporal $temporal): Temporal
            {
                $unit = $this->unit;
                if (!$temporal->supportsUnit($unit)) {
                    throw UnsupportedTemporalType::forUnit($unit);
                }

                if ($this->amount === 0) {
                    return $temporal;
                }

                $native = $temporal->toNative();
                $modification = $unit->toNativeModifier($this->amount);
                $modified = $native->modify($modification);

                return forward_static_call([get_class($temporal), 'fromNative'], $modified);
            }
        };
    }
}