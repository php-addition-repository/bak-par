<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Chrono\ChronoField;
use Par\Time\Exception\InvalidArgumentException;
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
 * @psalm-immutable
 * @extends Enum<Month>
 *
 * @method static self January() The singleton instance for the month of January with 31 days.
 * @method static self February() The singleton instance for the month of February with 28 days, or 29 in a leap year.
 * @method static self March() The singleton instance for the month of March with 31 days.
 * @method static self April() The singleton instance for the month of April with 30 days.
 * @method static self May() The singleton instance for the month of May with 31 days.
 * @method static self June() The singleton instance for the month of June with 30 days.
 * @method static self July() The singleton instance for the month of July with 31 days.
 * @method static self August() The singleton instance for the month of August with 31 days.
 * @method static self September() The singleton instance for the month of September with 30 days.
 * @method static self October() The singleton instance for the month of October with 31 days.
 * @method static self November() The singleton instance for the month of November with 30 days.
 * @method static self December() The singleton instance for the month of December with 31 days.
 */
final class Month extends Enum
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
     * @return self
     * @psalm-pure
     */
    public static function today(): self
    {
        return self::fromNative(Factory::today());
    }

    /**
     * Obtains an instance of DayOfWeek from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     * @psalm-pure
     */
    public static function fromNative(DateTimeInterface $dateTime): self
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
     * @return self
     * @throws InvalidArgumentException If the month-of-year is invalid
     * @psalm-pure
     */
    public static function of(int $month): self
    {
        ChronoField::MonthOfYear()->checkValidValue($month);

        return self::valueOf(self::VALUE_MAP[$month]);
    }

    /**
     * Obtains an instance of Month for tomorrow.
     *
     * @return self
     * @psalm-pure
     */
    public static function tomorrow(): self
    {
        return self::fromNative(Factory::tomorrow());
    }

    /**
     * Obtains an instance of Month for yesterday.
     *
     * @return self
     * @psalm-pure
     */
    public static function yesterday(): self
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
     * @return self The resulting month.
     */
    public function minus(int $months): self
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
     * @return self The resulting month.
     */
    public function plus(int $months): self
    {
        $currentValue = $this->value();
        $range = ChronoField::MonthOfYear()->range();
        $newValue = Range::calculateOverflow($currentValue, $months, $range->getMinimum(), $range->getMaximum());

        if ($newValue === $currentValue) {
            return $this;
        }

        return self::of($newValue);
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
     * @return self The resulting Month.
     */
    public function firstMonthOfQuarter(): self
    {
        if ($this->value() >= 10) {
            return self::October();
        }

        if ($this->value() >= 7) {
            return self::July();
        }

        if ($this->value() >= 4) {
            return self::April();
        }

        return self::January();
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
            /** @var self $month */
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
}