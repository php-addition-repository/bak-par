<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Hashable;
use Par\Time\Exception\InvalidArgumentException;

/**
 * A month-day in the ISO-8601 calendar system, such as 'December 3th'
 *
 * @psalm-immutable
 */
final class MonthDay implements Hashable
{
    private const DAY_OF_MONTH_FORMAT = 'd';

    private Month $month;
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
     *
     * @psalm-mutation-free
     */
    public static function of(int|Month $month, int $dayOfMonth): self
    {
        return new self($month, $dayOfMonth);
    }

    /**
     * Obtains the current month-day from the system clock in the default time-zone.
     *
     * @return self
     * @psalm-mutation-free
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
     * @psalm-mutation-free
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        /** @psalm-suppress ImpureMethodCall */
        return self::of(Month::fromNative($dateTime), (int)$dateTime->format(self::DAY_OF_MONTH_FORMAT));
    }

    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->hash() === $other->hash();
        }

        return false;
    }

    public function hash(): int
    {
        // 12-3 -> 1203
        return ($this->month->value() * 100) + $this->dayOfMonth;
    }

    /**
     * Gets the month field using the Month enum.
     */
    public function month(): Month
    {
        return $this->month;
    }

    /**
     * Gets the month field from 1 to 12.
     *
     * @see Month::value()
     */
    public function monthValue(): int
    {
        return $this->month->value();
    }

    /**
     * Gets the day-of-month field.
     */
    public function dayOfMonth(): int
    {
        return $this->dayOfMonth;
    }

    private function __construct(int|Month $month, int $dayOfMonth)
    {
        $this->month = is_int($month) ? Month::of($month) : $month;

        Assert::range($dayOfMonth, 1, $this->month->length(true));
        $this->dayOfMonth = $dayOfMonth;
    }
}