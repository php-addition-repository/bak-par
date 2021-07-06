<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Chrono\ChronoField;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAccessor;
use Par\Time\Temporal\TemporalAdjuster;
use Par\Time\Temporal\TemporalField;
use Par\Time\Util\Range;

/**
 * A month-of-year, such as 'July'.
 *
 * Month is an enum representing the 12 months of the year -
 * January, February, March, April, May, June, July, August, September, October, November and December.
 *
 * In addition to the textual enum name, each month-of-year has an int value. The int value follows normal usage and
 * the ISO-8601 standard, from 1 (January) to 12 (December). It is recommended that applications use the enum rather
 * than the int value to ensure code clarity.
 *
 * **Do not use ordinal() to obtain the numeric representation of Month. Use value() instead.**
 *
 * @extends Enum<Month>
 *
 * @method static static January() The singleton instance for the month of January with 31 days.
 * @method static static February() The singleton instance for the month of February with 28 days, or 29 in a leap year.
 * @method static static March() The singleton instance for the month of March with 31 days.
 * @method static static April() The singleton instance for the month of April with 30 days.
 * @method static static May() The singleton instance for the month of May with 31 days.
 * @method static static June() The singleton instance for the month of June with 30 days.
 * @method static static July() The singleton instance for the month of July with 31 days.
 * @method static static August() The singleton instance for the month of August with 31 days.
 * @method static static September() The singleton instance for the month of September with 30 days.
 * @method static static October() The singleton instance for the month of October with 31 days.
 * @method static static November() The singleton instance for the month of November with 30 days.
 * @method static static December() The singleton instance for the month of December with 31 days.
 */
final class Month extends Enum implements TemporalAccessor, TemporalAdjuster
{
    /**
     * @var array<int, string>
     */
    private const VALUE_MAP = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    /**
     * Obtains an instance of Month for today.
     *
     * @return static
     */
    public static function today(): static
    {
        return self::fromNative(Factory::today());
    }

    /**
     * Obtains an instance of DayOfWeek from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return static
     */
    public static function fromNative(DateTimeInterface $dateTime): static
    {
        return self::of(
            ChronoField::MonthOfYear()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains an instance of Month from an int value.
     *
     * @param int $month The month-of-year to represent, from 1 (January) to 12 (December)
     *
     * @return static
     * @throws InvalidArgumentException If the month-of-year is invalid
     */
    public static function of(int $month): static
    {
        ChronoField::MonthOfYear()->checkValidValue($month);

        return self::valueOf(self::VALUE_MAP[$month]);
    }

    /**
     * Obtains an instance of Month for tomorrow.
     *
     * @return static
     */
    public static function tomorrow(): static
    {
        return self::fromNative(Factory::tomorrow());
    }

    /**
     * Obtains an instance of Month for yesterday.
     *
     * @return static
     */
    public static function yesterday(): static
    {
        return self::fromNative(Factory::yesterday());
    }

    /**
     * Returns the month-of-year that is the specified number of months before this one.
     *
     * The calculation rolls around the start of the year from January to December. The specified period may be
     * negative.
     *
     * @param int $months The months to subtract, positive or negative
     *
     * @return static The resulting month.
     */
    public function minus(int $months): static
    {
        return $this->plus($months * -1);
    }

    /**
     * Returns the month-of-year that is the specified number of quarters after this one.
     *
     * The calculation rolls around the end of the year from December to January. The specified period may be negative.
     *
     * @param int $months The months to add, positive or negative
     *
     * @return static The resulting month.
     */
    public function plus(int $months): static
    {
        $currentValue = $this->value();
        $range = ChronoField::MonthOfYear()->range();
        $newValue = Range::calculateOverflow($currentValue, $months, $range->getMinimum(), $range->getMaximum());

        if ($newValue === $currentValue) {
            return $this;
        }

        return static::of($newValue);
    }

    /**
     * Gets the month-of-year int value.
     *
     * @return int
     */
    public function value(): int
    {
        return $this->ordinal() + 1;
    }

    /**
     * Gets the month corresponding to the first month of this quarter.
     *
     * The year can be divided into four quarters. This method returns the first month of the quarter for the base
     * month. January, February and March return January. April, May and June return April. July, August and
     * September return July. October, November and December return October.
     *
     * @return static The resulting Month.
     */
    public function firstMonthOfQuarter(): static
    {
        if ($this->value() >= 10) {
            return static::October();
        }

        if ($this->value() >= 7) {
            return static::July();
        }

        if ($this->value() >= 4) {
            return static::April();
        }

        return static::January();
    }

    /**
     * Gets the day-of-year corresponding to the first day of this month.
     *
     * This returns the day-of-year that this month begins on, using the leap year flag to determine the length of
     * February.
     *
     * @param bool $leapYear True if the length is required for a leap year
     *
     * @return int The resulting day
     */
    public function firstDayOfYear(bool $leapYear = false): int
    {
        $firstDay = 1;

        foreach (self::values() as $month) {
            if ($month->compareTo($this) < 0) {
                $firstDay += $month->length($leapYear);
            }
        }

        return $firstDay;
    }

    /**
     * Gets the length of this month in days.
     *
     * This takes a flag to determine whether to return the length for a leap year or not.
     *
     * February has 28 days in a standard year and 29 days in a leap year. April, June, September and November have 30
     * days. All other months have 31 days.
     *
     * @param bool $leapYear True if the length is required for a leap year
     *
     * @return int The resulting length.
     */
    public function length(bool $leapYear = false): int
    {
        return match ($this) {
            self::April(), self::June(), self::September(), self::November() => 30,
            self::February() => $leapYear ? 29 : 28,
            default => 31
        };
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return ChronoField::MonthOfYear()->equals($field);
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        if ($this->supportsField($field)) {
            return $this->value();
        }

        throw UnsupportedTemporalType::forField($field);
    }

    /**
     * @inheritDoc
     */
    public function adjustInto(Temporal $temporal): Temporal
    {
        return $temporal->withField(ChronoField::MonthOfYear(), $this->value());
    }
}
