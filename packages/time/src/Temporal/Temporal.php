<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

/**
 * Framework-level interface defining read-write access to a temporal object, such as a date, time, offset or some
 * combination of these. This is the base interface type for date, time and offset objects that are complete enough to
 * be manipulated using plus and minus. It is implemented by those classes that can provide and manipulate information
 * as fields or queries. See TemporalAccessor for the read-only version of this interface.
 *
 * Most date and time information can be represented as a number. These are modeled using TemporalField with the number
 * held using a long to handle large values. Year, month and day-of-month are simple examples of fields, but they also
 * include instant and offsets. See ChronoField for the standard set of fields.
 *
 * @internal
 */
interface Temporal extends TemporalAccessor
{
    /**
     * Returns an object of the same type as this object with the specified period added.
     *
     * This method returns a new object based on this one with the specified period subtracted. For example, on a
     * LocalDate, this could be used to subtract a number of years, months or days.
     *
     * @param int          $amountToSubtract The amount of the specified unit to subtract
     * @param TemporalUnit $unit             The unit of the amount to add
     *
     * @return static
     * @psalm-mutation-free
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): static;

    /**
     * Returns an object of the same type as this object with an amount subtracted.
     *
     * This adjusts this temporal, subtracting according to the rules of the specified amount. The amount is typically a
     * Period but may be any other type implementing the TemporalAmount interface, such as Duration.
     *
     * @param TemporalAmount $amount The amount to subtract
     *
     * @return static
     * @psalm-mutation-free
     */
    public function minusAmount(TemporalAmount $amount): static;

    /**
     * Returns an object of the same type as this object with the specified period added.
     *
     * This method returns a new object based on this one with the specified period added. For example, on a LocalDate,
     * this could be used to add a number of years, months or days.
     *
     * @param int          $amountToAdd The amount of the specified unit to add
     * @param TemporalUnit $unit        The unit of the amount to add
     *
     * @return static
     * @psalm-mutation-free
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): static;

    /**
     * Returns an object of the same type as this object with an amount added.
     *
     * This adjusts this temporal, adding according to the rules of the specified amount. The amount is typically a
     * Period but may be any other type implementing the TemporalAmount interface, such as Duration.
     *
     * @param TemporalAmount $amount The amount to add
     *
     * @return static
     * @psalm-mutation-free
     */
    public function plusAmount(TemporalAmount $amount): static;

    /**
     * Checks if the specified unit is supported.
     *
     * @param TemporalUnit $unit
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function supportsUnit(TemporalUnit $unit): bool;
}