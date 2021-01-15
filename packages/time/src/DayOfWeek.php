<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Util\Range;

/**
 * @psalm-immutable
 *
 * @method static self Monday()
 * @method static self Tuesday()
 * @method static self Wednesday()
 * @method static self Thursday()
 * @method static self Friday()
 * @method static self Saturday()
 * @method static self Sunday()
 */
final class DayOfWeek extends Enum
{
    private const MIN_VALUE = 1;
    private const MAX_VALUE = 7;

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
     * Obtains an instance of DayOfWeek for today.
     *
     * @return static
     * @psalm-pure
     */
    public static function today(): static
    {
        return static::fromNative(Factory::today());
    }

    /**
     * Obtains an instance of DayOfWeek from  an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return static
     * @psalm-pure
     */
    public static function fromNative(DateTimeInterface $dateTime): static
    {
        /** @psalm-suppress ImpureMethodCall */
        return static::of((int)$dateTime->format('N'));
    }

    /**
     * Obtains an instance of DayOfWeek from an int value.
     *
     * @param int $dayOfWeek The day-of-week to represent, from 1 (Monday) to 7 (Sunday)
     *
     * @return static
     * @throws InvalidArgumentException If the day-of-week is invalid
     * @psalm-pure
     */
    public static function of(int $dayOfWeek): static
    {
        Assert::range($dayOfWeek, static::MIN_VALUE, static::MAX_VALUE);

        return static::valueOf(static::VALUE_MAP[$dayOfWeek]);
    }

    /**
     * Obtains an instance of DayOfWeek for tomorrow.
     *
     * @return static
     * @psalm-pure
     */
    public static function tomorrow(): static
    {
        return static::fromNative(Factory::tomorrow());
    }

    /**
     * Obtains an instance of DayOfWeek for yesterday.
     *
     * @return static
     * @psalm-pure
     */
    public static function yesterday(): static
    {
        return static::fromNative(Factory::yesterday());
    }

    /**
     * Returns the day-of-week that is the specified number of days before this one.
     *
     * The calculation rolls around the start of the year from Monday to Sunday. The specified period may be negative.
     *
     * @param int $days The days to subtract, positive or negative
     *
     * @return DayOfWeek
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
     * @return DayOfWeek
     */
    public function plus(int $days): self
    {
        $currentValue = $this->value();
        $newValue = Range::calculateOverflow($currentValue, $days, self::MIN_VALUE, self::MAX_VALUE);

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
        return array_flip(static::VALUE_MAP)[$this->name()];
    }
}