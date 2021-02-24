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
use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\TemporalUnit;
use Par\Time\Traits\TemporalMathTrait;

/**
 * A date without a time-zone in the ISO-8601 calendar system, such as 2007-12-03.
 *
 * LocalDate is an immutable date-time object that represents a date, often viewed as year-month-day. Other date
 * fields, such as day-of-year, day-of-week and week-of-year, can also be accessed. For example, the value "2nd October
 * 2007" can be stored in a LocalDate.
 */
final class LocalDate implements Hashable, Comparable, Temporal
{
    use TemporalMathTrait;

    private int $year;
    private int $month;
    private int $dayOfMonth;

    /**
     * Obtains an instance of LocalDate from an implementation of the DateTimeInterface.
     *
     * @param DateTimeInterface $dateTime The datetime to convert
     *
     * @return static The local date
     */
    public static function fromNative(DateTimeInterface $dateTime): self
    {
        return new self(
            ChronoField::Year()->getFromNative($dateTime),
            ChronoField::MonthOfYear()->getFromNative($dateTime),
            ChronoField::DayOfMonth()->getFromNative($dateTime)
        );
    }

    /**
     * Obtains the current date from the system clock in the default time-zone.
     *
     * @return static The local date
     */
    public static function now(): self
    {
        return self::fromNative(Factory::now());
    }

    /**
     * Obtains the current date from the system clock in the default time-zone.
     *
     * Alias for LocalDate::now()
     *
     * @return static The local date
     */
    public static function today(): self
    {
        return self::now();
    }

    /**
     * Obtains yesterdays date from the system clock in the default time-zone.
     *
     * @return static The local date
     */
    public static function yesterday(): self
    {
        return self::fromNative(Factory::yesterday());
    }

    /**
     * Obtains tomorrows date from the system clock in the default time-zone.
     *
     * @return static The local date
     */
    public static function tomorrow(): self
    {
        return self::fromNative(Factory::tomorrow());
    }

    /**
     * Obtains an instance of LocalDate from a year, month and day.
     *
     * This returns a LocalDate with the specified year, month and day-of-month. The day must be valid for the year and
     * month, otherwise an exception will be thrown.
     *
     * @param int|Year  $year       The year to represent
     * @param int|Month $month      The month-of-year to represent
     * @param int       $dayOfMonth The day-of-month to represent
     *
     * @return static The local date
     */
    public static function of(int|Year $year, int|Month $month, int $dayOfMonth): self
    {
        $year = is_int($year) ? $year : $year->value();
        $month = is_int($month) ? $month : $month->value();

        return new self($year, $month, $dayOfMonth);
    }

    /**
     * Obtains an instance of LocalDate from a year and day-of-year.
     *
     * This returns a LocalDate with the specified year and day-of-year. The day-of-year must be valid for the year,
     * otherwise an exception will be thrown.
     *
     * @param int $year      The year to represent,
     * @param int $dayOfYear The day-of-year to represent, from 1 to 366
     *
     * @return static The local date
     */
    public static function ofYearDay(int $year, int $dayOfYear): self
    {
        ChronoField::DayOfYear()->checkValidValue($dayOfYear);

        $daysToAdd = $dayOfYear - 1;
        $firstDayOfYear = self::of($year, 1, 1);
        if ($dayOfYear === 0) {
            return $firstDayOfYear;
        }

        return $firstDayOfYear->plusDays($daysToAdd);
    }

    /**
     * Obtains an instance of LocalDate from a text string such as 2007-12-03.
     *
     * The string must represent a valid date.
     *
     * @param string $text The text to parse
     *
     * @return static The local date
     */
    public static function parse(string $text): self
    {
        Assert::regex($text, '/^\d{4}-\d{2}-\d{2}$/');

        preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $text, $matches);

        return new self((int)$matches[1], (int)$matches[2], (int)$matches[3]);
    }

    public function plusYears(int $amount): self
    {
        return $this->plus($amount, ChronoUnit::Years());
    }

    public function minusYears(int $amount): self
    {
        return $this->minus($amount, ChronoUnit::Years());
    }

    public function plusMonths(int $amount): self
    {
        return $this->plus($amount, ChronoUnit::Months());
    }

    public function minusMonths(int $amount): self
    {
        return $this->minus($amount, ChronoUnit::Months());
    }

    public function plusDays(int $amount): self
    {
        return $this->plus($amount, ChronoUnit::Days());
    }

    public function minusDays(int $amount): self
    {
        return $this->minus($amount, ChronoUnit::Days());
    }

    /**
     * @inheritDoc
     */
    public function toNative(): DateTimeImmutable
    {
        return Factory::createDate($this->year, $this->month, $this->dayOfMonth);
    }

    /**
     * Gets the year field.
     *
     * @return Year the year field
     */
    public function year(): Year
    {
        return Year::of($this->year);
    }

    /**
     * Gets the primitive value of the year field.
     *
     * This method returns the primitive int value for the year.
     *
     * @return int
     */
    public function yearValue(): int
    {
        return $this->year;
    }

    /**
     * Gets the month-of-year field using the Month enum.
     *
     * @return Month The month-of-year
     */
    public function month(): Month
    {
        return Month::of($this->month);
    }

    /**
     * Gets the month-of-year field from 1 to 12.
     *
     * This method returns the month as an int from 1 to 12. Application code is frequently clearer if the enum Month
     * is used by calling month().
     *
     * @return int The month-of-year, from 1 to 12
     */
    public function monthValue(): int
    {
        return $this->month;
    }

    /**
     * Gets the day-of-month field.
     *
     * This method returns the primitive int value for the day-of-month.
     *
     * @return int The day-of-month, from 1 to 31
     */
    public function dayOfMonth(): int
    {
        return $this->dayOfMonth;
    }

    /**
     * Gets the day-of-year field.
     *
     * This method returns the primitive int value for the day-of-year.
     *
     * @return int The day-of-year, from 1 to 365, or 366 in a leap year
     */
    public function dayOfYear(): int
    {
        return $this->get(ChronoField::DayOfYear());
    }

    /**
     * @inheritDoc
     */
    public function hash(): int
    {
        return (int)sprintf('%d%02d%02d', $this->year, $this->month, $this->dayOfMonth);
    }

    /**
     * @inheritDoc
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof self) {
            return $this->year === $other->year && $this->month === $other->month && $this->dayOfMonth === $other->dayOfMonth;
        }

        return false;
    }

    public function toString(): string
    {
        return sprintf('%d-%02d-%02d', $this->year, $this->month, $this->dayOfMonth);
    }

    /**
     * @inheritDoc
     */
    public function compareTo(Comparable $other): int
    {
        if ($other instanceof static) {
            return $this->hash() <=> $other->hash();
        }

        throw ClassMismatch::forUnexpectedInstance($this, $other);
    }

    /**
     * @inheritDoc
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        return $unit->isDateBased();
    }

    /**
     * @inheritDoc
     */
    public function with(TemporalAdjuster $adjuster): Temporal
    {
        return $adjuster->adjustInto($this);
    }

    /**
     * @inheritDoc
     */
    public function withField(TemporalField $field, int $newValue): Temporal
    {
        if (!$this->supportsField($field)) {
            throw UnsupportedTemporalType::forField($field);
        }

        return match ($field) {
            ChronoField::Year() => $this->withYear($newValue),
            ChronoField::MonthOfYear() => $this->withMonth($newValue),
            ChronoField::DayOfMonth() => $this->withDayOfMonth($newValue),
            ChronoField::DayOfYear() => $this->withDayOfYear($newValue),
            ChronoField::DayOfWeek() => $this->with(TemporalAdjusters::nextOrSame(DayOfWeek::of($newValue))),
        };
    }

    public function withYear(int|Year $year): self
    {
        return self::of($year, $this->month, $this->dayOfMonth);
    }

    public function withMonth(int|Month $month): self
    {
        return self::of($this->year, $month, $this->dayOfMonth);
    }

    public function withDayOfYear(int $dayOfYear): self
    {
        return self::ofYearDay($this->year, $dayOfYear);
    }

    public function withDayOfMonth(int $dayOfMonth): self
    {
        return self::of($this->year, $this->month, $dayOfMonth);
    }

    /**
     * @inheritDoc
     */
    public function supportsField(TemporalField $field): bool
    {
        return $field->isDateBased();
    }

    /**
     * @inheritDoc
     */
    public function get(TemporalField $field): int
    {
        if (!$this->supportsField($field)) {
            throw UnsupportedTemporalType::forField($field);
        }

        return match ($field) {
            ChronoField::Year() => $this->year,
            ChronoField::MonthOfYear() => $this->month,
            ChronoField::DayOfMonth() => $this->dayOfMonth,
            default => $field->getFromNative($this->toNative()),
        };
    }

    private function __construct(int $year, int $month, int $dayOfMonth)
    {
        ChronoField::Year()->checkValidValue($year);
        ChronoField::MonthOfYear()->checkValidValue($month);
        ChronoField::DayOfMonth()->checkValidValue($dayOfMonth);

        Assert::date($year, $month, $dayOfMonth);

        $this->year = $year;
        $this->month = $month;
        $this->dayOfMonth = $dayOfMonth;
    }
}