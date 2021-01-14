<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Exception\InvalidArgumentException;

/**
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
     * Obtains an instance of DayOfWeek from  an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return static
     */
    public static function fromNative(DateTimeInterface $dateTime): static
    {
        return static::of((int)$dateTime->format('N'));
    }

    /**
     * Obtains an instance of DayOfWeek from an int value.
     *
     * @param int $dayOfWeek The day-of-week to represent, from 1 (Monday) to 7 (Sunday)
     *
     * @return static
     * @throws InvalidArgumentException If the day-of-week is invalid
     */
    public static function of(int $dayOfWeek): static
    {
        Assert::range($dayOfWeek, static::MIN_VALUE, static::MAX_VALUE);

        return static::valueOf(static::VALUE_MAP[$dayOfWeek]);
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
        return $this->ordinal() + 1;
    }
}