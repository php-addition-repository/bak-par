<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Comparable;
use Par\Core\Exception\ClassMismatch;
use Par\Core\Hashable;
use Par\Time\Chrono\ChronoField;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAccessor;
use Par\Time\Temporal\TemporalAdjuster;
use Par\Time\Temporal\TemporalField;

/**
 * A month-day in the ISO-8601 calendar system, such as '--12-03'.
 *
 * MonthDay is an immutable date-time object that represents the combination of a month and day-of-month. Any field
 * that can be derived from a month and day, such as quarter-of-year, can be obtained.
 *
 * This class does not store or represent a year, time or time-zone. For example, the value "December 3rd" can be
 * stored in a MonthDay.
 *
 * This class does not store or represent a year, time or time-zone. For example, the value "December 3rd" can be
 * stored in a MonthDay. Since a MonthDay does not possess a year, the leap day of February 29th is considered valid.
 *
 * @template-implements Comparable<MonthDay>
 */
final class MonthDay implements Hashable, Comparable, TemporalAccessor, TemporalAdjuster
{
    private int $month;
    private int $dayOfMonth;

    /**
     * Obtains an instance of month-day.
     *
     * The day-of-month must be valid for the month within a leap year. Hence, for month 2 (February), day 29 is valid.
     * For example, passing in month 4 (April) and day 31 will throw an exception, as there can never be April 31st in
     * any year. By contrast, passing in February 29th is permitted, as that month-day can sometimes be valid.
     *
     * @param int|Month $month      The month-of-year to represent, from 1 (January) to 12 (December)
     * @param int       $dayOfMonth The day-of-month to represent, from 1 to 31
     *
     * @return self
     * @throws InvalidArgumentException If value of any field is out of range, or if the day-of-month is invalid for
     *                                  the month
     */
    public static function of(int|Month $month, int $dayOfMonth): self
    {
        $month = is_int($month) ? $month : $month->value();

        return new self($month, $dayOfMonth);
    }

    /**
     * Obtains the current month-day from the system clock in the default time-zone.
     *
     * @return self
     */
    public static function now(): self
    {
        $now = Factory::now();

        return self::fromNative($now);
    }

    /**
     * Obtains an instance of month-day from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        return self::of(
            ChronoField::MonthOfYear()->getFromNative($dateTime),
            ChronoField::DayOfMonth()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains an instance of MonthDay from a text string such as --12-03.
     *
     * The string must represent a valid month-day. The format is --MM-dd.
     *
     * @param string $text The text to parse such as "--12-03"
     *
     * @return self The parsed month-day
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^--\d{2}-\d{2}$/');

        preg_match('/^--(\d{2})-(\d{2})$/', $text, $matches);

        $month = Month::of((int)$matches[1]);

        return self::of($month, (int)$matches[2]);
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true =MonthDay $other
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->dayOfMonth === $other->dayOfMonth && $this->month === $other->month;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function hash(): int
    {
        // 12-3 -> 1203
        return ($this->month * 100) + $this->dayOfMonth;
    }

    /**
     * Gets the month field using the Month enum.
     */
    public function month(): Month
    {
        return Month::of($this->month);
    }

    /**
     * Gets the month field from 1 to 12.
     *
     * @see Month::value()
     */
    public function monthValue(): int
    {
        return $this->month;
    }

    /**
     * Gets the day-of-month field.
     */
    public function dayOfMonth(): int
    {
        return $this->dayOfMonth;
    }

    /**
     * Returns a copy of this MonthDay with the month-of-year altered.
     *
     * This returns a month-day with the specified month. If the day-of-month is invalid for the specified month, the
     * day will be adjusted to the last valid day-of-month.
     *
     * @param int|Month $month The month-of-year to set in the returned month-day
     *
     * @return self A MonthDay based on this month-day with the requested month
     */
    public function withMonth(int|Month $month): self
    {
        $month = is_int($month) ? $month : $month->value();

        return new self($month, $this->dayOfMonth);
    }

    /**
     * Returns a copy of this MonthDay with the day-of-month altered.
     *
     * This returns a month-day with the specified day-of-month. If the day-of-month is invalid for the month, an
     * exception is thrown.
     *
     * @param int $dayOfMonth The day-of-month to set in the return month-day, from 1 to 31
     *
     * @return self A MonthDay based on this month-day with the requested day
     */
    public function withDayOfMonth(int $dayOfMonth): self
    {
        return new self($this->month, $dayOfMonth);
    }

    /**
     * Outputs this month-day as a String, such as --12-03.
     *
     * The output will be in the format --m-d.
     *
     * @return string
     */
    public function toString(): string
    {
        return sprintf('--%02d-%02d', $this->month, $this->dayOfMonth);
    }

    /**
     * @@inheritDoc
     */
    public function compareTo(Comparable $other): int
    {
        if ($other instanceof static) {
            $currentValue = ($this->month * 100) + $this->dayOfMonth;
            $otherValue = ($other->month * 100) + $other->dayOfMonth;

            return $currentValue <=> $otherValue;
        }

        throw ClassMismatch::forExpectedInstance($this, $other);
    }

    /**
     * Checks if this month-day is after the specified month-day.
     *
     * @param MonthDay $other The other month-day to compare to
     *
     * @return bool
     */
    public function isAfter(MonthDay $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    /**
     * Checks if this month-day is before the specified month-day.
     *
     * @param MonthDay $other The other month-day to compare to
     *
     * @return bool
     */
    public function isBefore(MonthDay $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return match ($field) {
            ChronoField::DayOfMonth(), ChronoField::MonthOfYear() => true,
            default => false
        };
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        return match ($field) {
            ChronoField::DayOfMonth() => $this->dayOfMonth,
            ChronoField::MonthOfYear() => $this->month,
            default => throw UnsupportedTemporalType::forField($field),
        };
    }

    /**
     * @inheritDoc
     */
    public function adjustInto(Temporal $temporal): Temporal
    {
        return $temporal->withField(ChronoField::MonthOfYear(), $this->month)
                        ->withField(ChronoField::DayOfMonth(), $this->dayOfMonth);
    }

    private function __construct(int $month, int $dayOfMonth)
    {
        ChronoField::MonthOfYear()->checkValidValue($month);
        $this->month = $month;

        ChronoField::DayOfMonth()->checkValidValue($dayOfMonth);
        Assert::range($dayOfMonth, 1, Month::of($month)->length(true));

        $this->dayOfMonth = $dayOfMonth;
    }
}