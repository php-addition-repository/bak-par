<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Core\Hashable;

/**
 * A unit of date-time, such as Days or Hours.
 *
 * Measurement of time is built on units, such as years, months, days, hours, minutes and seconds. Implementations of
 * this interface represent those units.
 *
 * The unit works using double dispatch. Client code calls methods on a date-time like LocalDateTime which check if the
 * unit is a ChronoUnit. If it is, then the date-time must handle it. Otherwise, the method call is re-dispatched to
 * the matching method in this interface.
 */
interface TemporalUnit extends Hashable
{
    /**
     * Checks if this unit is a date unit.
     *
     * All units from days to millenia inclusive are date-based. Time-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isDateBased(): bool;

    /**
     * Checks if the duration of the unit is an estimate.
     *
     * All time units in this class are considered to be accurate, while all date units in this class are considered to
     * be estimated.
     *
     * This definition ignores leap seconds, but considers that Days vary due to daylight saving time and months have
     * different lengths.
     *
     * @return bool
     */
    public function isDurationEstimated(): bool;

    /**
     * Checks if this unit is supported by the specified temporal object.
     *
     * This checks that the implementing date-time can add/subtract this unit.
     * This can be used to avoid throwing an exception.
     *
     * @param Temporal $temporal The temporal object to check
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function isSupportedBy(Temporal $temporal): bool;

    /**
     * Checks if this unit is a time unit.
     *
     * All units from micros to half-days inclusive are time-based. Date-based units and FOREVER return false.
     *
     * @return bool
     */
    public function isTimeBased(): bool;
}