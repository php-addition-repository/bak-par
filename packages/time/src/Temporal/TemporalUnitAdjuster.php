<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Time\Exception\UnsupportedTemporalType;

/**
 * @internal
 */
final class TemporalUnitAdjuster implements TemporalAdjuster
{
    public function __construct(private int $amount, private TemporalUnit $unit)
    {
    }

    /**
     * @inheritDoc
     *
     * @psalm-mutation-free
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
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
}