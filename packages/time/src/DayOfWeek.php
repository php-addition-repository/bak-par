<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Chrono\ChronoField;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\TemporalAccessor;
use Par\Time\Temporal\TemporalField;
use Par\Time\Util\Range;

/**
 * A day-of-week, such as 'Tuesday'.
 *
 * DayOfWeek is an enum representing the 7 days of the week - Monday, Tuesday, Wednesday, Thursday, Friday, Saturday
 * and Sunday. In addition to the textual enum name, each day-of-week has an int value. The int value follows the
 * ISO-8601 standard, from 1 (Monday) to 7 (Sunday). It is recommended that applications use the enum rather than the
 * int value to ensure code clarity.
 *
 * @extends Enum<DayOfWeek>
 *
 * @method static self Monday() The singleton instance for the day-of-week of Monday.
 * @method static self Tuesday() The singleton instance for the day-of-week of Tuesday.
 * @method static self Wednesday() The singleton instance for the day-of-week of Wednesday.
 * @method static self Thursday() The singleton instance for the day-of-week of Thursday.
 * @method static self Friday() The singleton instance for the day-of-week of Friday.
 * @method static self Saturday() The singleton instance for the day-of-week of Saturday.
 * @method static self Sunday() The singleton instance for the day-of-week of Sunday.
 */
final class DayOfWeek extends Enum implements TemporalAccessor
{
    /**
     * @var array<int, string>
     */
    private const VALUE_MAP = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    /**
     * Obtains an instance of day-of-week for today.
     *
     * @return self
     */
    public static function today(): self
    {
        return self::fromNative(Factory::today());
    }

    /**
     * Obtains an instance of day-of-week from  an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        return self::of(
            ChronoField::DayOfWeek()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains an instance of day-of-week from an int value.
     *
     * @param int $dayOfWeek The day-of-week to represent, from 1 (Monday) to 7 (Sunday)
     *
     * @return self
     * @throws InvalidArgumentException If the day-of-week is invalid
     */
    public static function of(int $dayOfWeek): self
    {
        ChronoField::DayOfWeek()->checkValidValue($dayOfWeek);

        return self::valueOf(self::VALUE_MAP[$dayOfWeek]);
    }

    /**
     * Obtains an instance of day-of-week for tomorrow.
     *
     * @return self
     */
    public static function tomorrow(): self
    {
        return self::fromNative(Factory::tomorrow());
    }

    /**
     * Obtains an instance of day-of-week for yesterday.
     *
     * @return self
     */
    public static function yesterday(): self
    {
        return self::fromNative(Factory::yesterday());
    }

    /**
     * Returns the day-of-week that is the specified number of days before this one.
     *
     * The calculation rolls around the start of the year from Monday to Sunday. The specified period may be negative.
     *
     * @param int $days The days to subtract, positive or negative
     *
     * @return self The resulting day-of-week
     */
    public function minus(int $days): self
    {
        return $this->plus($days * -1);
    }

    /**
     * Returns the day-of-week that is the specified number of days after this one.
     *
     * The calculation rolls around the end of the week from Sunday to Monday. The specified period may be negative.
     *
     * @param int $days The days to add, positive or negative
     *
     * @return self The resulting day-of-week
     */
    public function plus(int $days): self
    {
        $currentValue = $this->value();
        $range = ChronoField::DayOfWeek()->range();
        $newValue = Range::calculateOverflow($currentValue, $days, $range->getMinimum(), $range->getMaximum());

        if ($newValue === $currentValue) {
            return $this;
        }

        return self::of($newValue);
    }

    /**
     * Returns the day-of-week int value.
     *
     * The values are numbered following the ISO-8601 standard, from 1 (Monday) to 7 (Sunday).
     *
     * @return int The day-of-week, from 1 (Monday) to 7 (Sunday)
     */
    public function value(): int
    {
        return array_flip(self::VALUE_MAP)[$this->name()];
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return ChronoField::DayOfWeek()->equals($field);
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

}