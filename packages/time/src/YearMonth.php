<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use DateTimeInterface;
use Par\Core\Comparable;
use Par\Core\Exception\ClassMismatch;
use Par\Core\Hashable;
use Par\Time\Chrono\ChronoField;
use Par\Time\Chrono\ChronoUnit;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjuster;
use Par\Time\Temporal\TemporalAdjusters;
use Par\Time\Temporal\TemporalAmount;
use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\TemporalUnit;

/**
 * A year-month in the ISO-8601 calendar system, such as 2007-12.
 *
 * YearMonth is an immutable date-time object that represents the combination of a year and month. Any field that can
 * be derived from a year and month, such as quarter-of-year, can be obtained.
 *
 * This class does not store or represent a day, time or time-zone. For example, the value "October 2007" can be stored
 * in a YearMonth.
 *
 * @psalm-immutable
 * @template-implements Comparable<YearMonth>
 */
final class YearMonth implements Hashable, Comparable, Temporal
{
    private int $year;
    private int $month;

    /**
     * Obtains an instance of YearMonth from a year and month.
     *
     * @param int|Year  $year  The year to represent
     * @param int|Month $month The month-of-year to represent
     *
     * @return self
     * @psalm-mutation-free
     */
    public static function of(int|Year $year, int|Month $month): self
    {
        $year = is_int($year) ? $year : $year->value();
        $month = is_int($month) ? $month : $month->value();

        return new self($year, $month);
    }

    /**
     * Obtains an instance of year-month from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return self
     * @psalm-mutation-free
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        return self::of(
            ChronoField::Year()->getFromNative($dateTime),
            ChronoField::MonthOfYear()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains the current year-month from the system clock in the default time-zone.
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
     * Obtains an instance of YearMonth from a text string such as 2007-03.
     *
     * The string must represent a valid year-month. The format is Y-m.
     *
     * @param string $text The text to parse such as "2007-03"
     *
     * @return self The parsed year-month
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^-?\d{1,9}-\d{2}$/');

        preg_match('/^(-?\d{1,9})-(\d{2})$/', $text, $matches);

        return new self((int)$matches[1], (int)$matches[2]);
    }

    /**
     * @inheritDoc
     *
     * @return int
     */
    public function hash(): int
    {
        // 12-3 -> 1203
        return ($this->year * 100) + $this->month;
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true =YearMonth $other
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->year === $other->year && $this->month === $other->month;
        }

        return false;
    }

    /**
     * Outputs this year-month as a String, such as 2007-12.
     *
     * The output will be in the format Y-m.
     *
     * @return string
     */
    public function toString(): string
    {
        return sprintf('%d-%02d', $this->year, $this->month);
    }

    /**
     * Gets the year field.
     *
     * @return Year
     */
    public function year(): Year
    {
        return Year::of($this->year);
    }

    /**
     * Gets the primitive value of the year field.
     *
     * @return int
     * @see Year::value()
     */
    public function yearValue(): int
    {
        return $this->year;
    }

    /**
     * Gets the month-of-year field using the Month enum.
     *
     * @return Month
     */
    public function month(): Month
    {
        return Month::of($this->month);
    }

    /**
     * Gets the month-of-year field from 1 to 12.
     *
     * @return int
     * @see Month::value()
     */
    public function monthValue(): int
    {
        return $this->month;
    }

    /**
     * Returns a copy of this YearMonth with the month-of-year altered.
     *
     * @param int|Month $month The month-of-year to set in the returned year-month, from 1 (January) to 12 (December)
     *
     * @return self A YearMonth based on this year-month with the requested month
     */
    public function withMonth(int|Month $month): self
    {
        return self::of($this->year, $month);
    }

    /**
     * Returns a copy of this YearMonth with the year altered.
     *
     * @param int|Year $year The year to set in the returned year-month
     *
     * @return self A YearMonth based on this year-month with the requested year
     */
    public function withYear(int|Year $year): self
    {
        return self::of($year, $this->month);
    }

    /**
     * @@inheritDoc
     */
    public function compareTo(Comparable $other): int
    {
        if ($other instanceof static) {
            $currentValue = ($this->yearValue() * 100) + $this->monthValue();
            $otherValue = ($other->yearValue() * 100) + $other->monthValue();

            return $currentValue <=> $otherValue;
        }

        throw ClassMismatch::forExpectedInstance($this, $other);
    }

    /**
     * Checks if this year-month is after the specified year-month.
     *
     * @param YearMonth $other The other year-month to compare to
     *
     * @return bool
     */
    public function isAfter(YearMonth $other): bool
    {
        return $this->compareTo($other) > 0;
    }

    /**
     * Checks if this year-month is before the specified year-month.
     *
     * @param YearMonth $other The other year-month to compare to
     *
     * @return bool
     */
    public function isBefore(YearMonth $other): bool
    {
        return $this->compareTo($other) < 0;
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return match ($field) {
            ChronoField::Year(), ChronoField::MonthOfYear() => true,
            default => false
        };
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        return match ($field) {
            ChronoField::Year() => $this->year,
            ChronoField::MonthOfYear() => $this->month,
            default => throw UnsupportedTemporalType::forField($field),
        };
    }

    /**
     * @inheritDoc
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): self
    {
        return $this->plus($amountToSubtract * -1, $unit);
    }

    /**
     * @inheritDoc
     */
    public function minusAmount(TemporalAmount $amount): self
    {
        /** @var static $temporal */
        $temporal = $amount->subtractFrom($this);

        return $temporal;
    }

    /**
     * @inheritDoc
     */
    public function toNative(): DateTimeImmutable
    {
        return Factory::createDate($this->year, $this->month);
    }

    /**
     * @inheritDoc
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): self
    {
        /** @psalm-var TemporalAdjuster<YearMonth> $adjuster */
        $adjuster = TemporalAdjusters::plusUnit($amountToAdd, $unit);

        return $this->with($adjuster);
    }

    /**
     * @inheritDoc
     */
    public function with(TemporalAdjuster $adjuster): self
    {
        return $adjuster->adjustInto($this);
    }

    public function withField(TemporalField $field, int $newValue): Temporal
    {
        if (!$this->supportsField($field)) {
            throw UnsupportedTemporalType::forField($field);
        }

        return match ($field) {
            ChronoField::Year() => self::of($newValue, $this->month),
            ChronoField::MonthOfYear() => self::of($this->year, $newValue)
        };
    }

    /**
     * @inheritDoc
     */
    public function plusAmount(TemporalAmount $amount): self
    {
        /** @var static $temporal */
        $temporal = $amount->addTo($this);

        return $temporal;
    }

    public function plusYears(int $amountToAdd): self
    {
        return $this->plus($amountToAdd, ChronoUnit::Years());
    }

    public function minusYears(int $amountToSubtract): self
    {
        return $this->minus($amountToSubtract, ChronoUnit::Years());
    }

    public function plusMonths(int $amountToAdd): self
    {
        return $this->plus($amountToAdd, ChronoUnit::Months());
    }

    public function minusMonths(int $amountToSubtract): self
    {
        return $this->minus($amountToSubtract, ChronoUnit::Months());
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        return match ($unit) {
            ChronoUnit::Months(), ChronoUnit::Years(), ChronoUnit::Decades(), ChronoUnit::Centuries(
            ), ChronoUnit::Millennia() => true,
            default => false
        };
    }

    private function __construct(int $year, int $month)
    {
        ChronoField::Year()->checkValidValue($year);
        ChronoField::MonthOfYear()->checkValidValue($month);

        $this->year = $year;
        $this->month = $month;
    }

}