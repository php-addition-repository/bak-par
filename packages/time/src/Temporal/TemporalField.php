<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use DateTimeInterface;

/**
 * A field of date-time, such as month-of-year or hour-of-minute.
 *
 * Date and time is expressed using fields which partition the time-line into something meaningful for humans.
 * Implementations of this interface represent those fields.
 *
 * The field works using double dispatch. Client code calls methods on a date-time like LocalDateTime which check if
 * the field is a ChronoField. If it is, then the date-time must handle it. Otherwise, the method call is re-dispatched
 * to the matching method in this interface.
 */
interface TemporalField
{
    /**
     * Gets the unit that the field is measured in.
     *
     * The unit of the field is the period that varies within the range. For example, in the field 'MonthOfYear', the
     * unit is 'Months'.
     *
     * @return TemporalUnit
     * @see TemporalField::getRangeUnit
     * @psalm-mutation-free
     */
    public function getBaseUnit(): TemporalUnit;

    /**
     * Obtains the value of current field from a native DateTimeInterface object.
     *
     * @param DateTimeInterface $dateTime The DateTimeInterface object to obtain value from
     *
     * @return int
     * @psalm-mutation-free
     */
    public function getFromNative(DateTimeInterface $dateTime): int;

    /**
     * Gets the range that the field is bound by.
     *
     * The range of the field is the period that the field varies within. For example, in the field 'MonthOfYear', the
     * range is 'Years'.
     *
     * @return TemporalUnit
     * @see TemporalField::getBaseUnit
     * @psalm-mutation-free
     */
    public function getRangeUnit(): TemporalUnit;

    /**
     * Checks if this field represents a component of a date.
     *
     * A field is date-based if it can be derived from EPOCH_DAY. Note that it is valid for both isDateBased() and
     * isTimeBased() to return false, such as when representing a field like minute-of-week.
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function isDateBased(): bool;

    /**
     * Checks if this field is supported by the temporal object.
     *
     * @param TemporalAccessor $temporalAccessor
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function isSupportedBy(TemporalAccessor $temporalAccessor): bool;

    /**
     * Checks if this field represents a component of a time.
     *
     * A field is time-based if it can be derived from MICRO_OF_DAY. Note that it is valid for both isDateBased() and
     * isTimeBased() to return false, such as when representing a field like minute-of-week.
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function isTimeBased(): bool;

    /**
     * Gets the range of valid values for the field.
     *
     * @return ValueRange
     * @psalm-mutation-free
     */
    public function range(): ValueRange;

    /**
     * Gets a descriptive name for the field.
     *
     * The should be of the format 'BaseOfRange', such as 'MonthOfYear', unless the field has a range of FOREVER, when
     * only the base unit is mentioned, such as 'Year' or 'Era'.
     *
     * @return string
     * @psalm-mutation-free
     */
    public function toString(): string;
}