<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeInterface;
use Par\Core\Comparable;
use Par\Core\Exception\ClassMismatch;
use Par\Core\Hashable;
use Par\Time\Exception\InvalidArgumentException;
use Stringable;

/**
 * A year in the ISO-8601 calendar system, such as 2007.
 *
 * @psalm-immutable
 * @template-implements Comparable<Year>
 */
final class Year implements Hashable, Stringable, Comparable
{
    public const MIN_VALUE = -999999999;
    public const MAX_VALUE = 999999999;

    private const DATE_FORMAT = 'Y';

    private int $value;

    /**
     * Obtains the current year from the system clock in the default time-zone.
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
     * Obtains an instance of DayOfWeek from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     * @psalm-mutation-free
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        /** @psalm-suppress ImpureMethodCall */
        return self::of((int)$dateTime->format(self::DATE_FORMAT));
    }

    /**
     * Obtains an instance of Year.
     *
     * @param int $year The year to represent
     *
     * @return self
     * @psalm-mutation-free
     */
    public static function of(int $year): self
    {
        return new self($year);
    }

    /**
     * Obtains an instance of Year from a text string such as 2007.
     *
     * @param string $text The text to parse
     *
     * @return self
     * @throws InvalidArgumentException If text could not be parsed or value is outside of range
     * @psalm-mutation-free
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^[-+]?\d{1,}$/');

        return new self((int)$text);
    }

    /**
     * Checks if the year is a leap year, according to the ISO calendar system rules.
     *
     * @param int $year
     *
     * @return bool
     * @psalm-mutation-free
     */
    public static function isLeapYear(int $year): bool
    {
        if ($year === 0) {
            return false;
        }
        $dt = Factory::createDate($year);

        return (int)$dt->format('L') === 1;
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true =Year $other
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->hash() === $other->hash();
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @return int
     */
    public function hash(): int
    {
        return $this->value;
    }

    /**
     * Gets the year value.
     *
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * Gets the length of this year in days.
     *
     * @return int 365 or 366
     */
    public function length(): int
    {
        return $this->isLeap() ? 366 : 365;
    }

    /**
     * Checks if the year is a leap year, according to the ISO calendar system rules.
     *
     * @return bool
     * @see Year::isLeapYear
     */
    public function isLeap(): bool
    {
        return static::isLeapYear($this->value);
    }

    /**
     * Outputs this year as a string.
     *
     * @return string A string representation of this year
     */
    public function toString(): string
    {
        return (string)$this;
    }

    /**
     * Outputs this year as a string.
     *
     * @return string A string representation of this year
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @inheritDoc
     */
    public function compareTo(Comparable $other): int
    {
        if ($other instanceof static) {
            return $this->value <=> $other->value;
        }

        throw ClassMismatch::forExpectedInstance($this, $other);
    }

    /**
     * @throws InvalidArgumentException If year is outside of range
     */
    private function __construct(int $year)
    {
        Assert::range($year, self::MIN_VALUE, self::MAX_VALUE);

        $this->value = $year;
    }
}