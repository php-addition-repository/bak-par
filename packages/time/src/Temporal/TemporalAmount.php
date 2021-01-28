<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Time\Exception\UnsupportedTemporalType;

/**
 * Framework-level interface defining an amount of time, such as "6 hours", "8 days" or "2 years and 3 months".
 *
 * @internal
 */
interface TemporalAmount
{
    /**
     * Adds to the specified temporal object.
     *
     * @param Temporal $temporal The temporal object to add the amount to
     *
     * @return Temporal An object of the same observable type with the addition made
     *
     * @template T of Temporal
     * @psalm-param T  $temporal
     * @psalm-return T
     */
    public function addTo(Temporal $temporal): Temporal;

    /**
     * @param Temporal $temporal
     *
     * @return Temporal An object of the same observable type with the addition made
     *
     * @template T of Temporal
     * @psalm-param T  $temporal
     * @psalm-return T
     */
    public function subtractFrom(Temporal $temporal): Temporal;

    /**
     * Returns the list of units uniquely defining the value of this TemporalAmount. The list of TemporalUnits is
     * defined by the implementation class. The list is a snapshot of the units at the time getUnits is called and is
     * not mutable. The units are ordered from longest duration to the shortest duration of the unit.
     *
     * @return TemporalUnit[]
     */
    public function getUnits(): array;

    /**
     * Returns the value of the requested unit. The units returned from getUnits() uniquely define the value of the
     * TemporalAmount. A value must be returned for each unit listed in getUnits.
     *
     * @param TemporalUnit $unit
     *
     * @return int
     * @throws UnsupportedTemporalType If the unit is not supported
     */
    public function get(TemporalUnit $unit): int;
}