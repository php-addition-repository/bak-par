<?php

namespace Par\Time\Temporal;

/**
 * Strategy for adjusting a temporal object.
 *
 * Adjusters are a key tool for modifying temporal objects. They exist to externalize the process of adjustment,
 * permitting different approaches, as per the strategy design pattern. Examples might be an adjuster that sets the
 * date avoiding weekends, or one that sets the date to the last day of the month.
 *
 */
interface TemporalAdjuster
{
    /**
     * Adjusts the specified temporal object.
     *
     * This adjusts the specified temporal object using the logic encapsulated in the implementing class. Examples
     * might be an adjuster that sets the date avoiding weekends, or one that sets the date to the last day of the
     * month.
     *
     * @param Temporal $temporal The temporal object to adjust
     *
     * @return Temporal An object of the same observable type with the adjustment made
     *
     * @template T of Temporal
     * @psalm-param T  $temporal
     * @psalm-return T
     */
    public function adjustInto(Temporal $temporal): Temporal;
}