<?php

declare(strict_types=1);

namespace Par\Time\Chrono;

use DateTimeInterface;
use Par\Core\Enum;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Temporal\TemporalAccessor;
use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\TemporalUnit;
use Par\Time\Temporal\ValueRange;

/**
 * A standard set of fields.
 *
 * This set of fields provide field-based access to manipulate a date, time or date-time. The standard set of fields
 * can be extended by implementing TemporalField.
 *
 * @psalm-immutable
 * @extends Enum<ChronoUnit>
 *
 * @method static self DayOfWeek()
 * @method static self DayOfMonth()
 * @method static self MonthOfYear()
 * @method static self Year()
 */
final class ChronoField extends Enum implements TemporalField
{
    /**
     * @inheritDoc
     */
    public function getBaseUnit(): TemporalUnit
    {
        return match ($this) {
            self::DayOfWeek(), self::DayOfMonth() => ChronoUnit::Days(),
            self::MonthOfYear() => ChronoUnit::Months(),
            self::Year() => ChronoUnit::Years(),
        };
    }

    /**
     * @inheritDoc
     */
    public function getFromNative(DateTimeInterface $dateTime): int
    {
        $format = match ($this) {
            self::DayOfWeek() => 'N',
            self::DayOfMonth() => 'j',
            self::MonthOfYear() => 'n',
            self::Year() => 'Y',
        };

        /**
         * @psalm-suppress ImpureMethodCall
         */
        return (int)$dateTime->format($format);
    }

    /**
     * @inheritDoc
     */
    public function getRangeUnit(): TemporalUnit
    {
        return match ($this) {
            self::DayOfWeek() => ChronoUnit::Weeks(),
            self::DayOfMonth() => ChronoUnit::Months(),
            self::MonthOfYear() => ChronoUnit::Years(),
            self::Year() => ChronoUnit::Forever(),
        };
    }

    /**
     * @inheritDoc
     */
    public function isDateBased(): bool
    {
        return $this->getBaseUnit()->isDateBased()
            && ($this->getRangeUnit()->isDateBased() || $this->getRangeUnit()->equals(ChronoUnit::Forever()));
    }

    /**
     * @inheritDoc
     */
    public function isSupportedBy(TemporalAccessor $temporalAccessor): bool
    {
        return $temporalAccessor->supportsField($this);
    }

    /**
     * @inheritDoc
     */
    public function isTimeBased(): bool
    {
        return $this->getBaseUnit()->isTimeBased()
            && ($this->getRangeUnit()->isTimeBased() || $this->getRangeUnit()->equals(ChronoUnit::Forever()));
    }

    /**
     * @inheritDoc
     */
    public function range(): ValueRange
    {
        $rangeValues = match ($this) {
            self::DayOfWeek() => [1, 7],
            self::DayOfMonth() => [1, 28, 31],
            self::MonthOfYear() => [1, 12],
            self::Year() => [-999999999, 999999999],
            default => [PHP_INT_MIN, PHP_INT_MAX]
        };

        return $this->createRange($rangeValues);
    }

    /**
     * Checks that the specified value is valid for this field.
     *
     * This validates that the value is within the outer range of valid values returned by range().
     *
     * @param int $value
     *
     * @return void
     * @throws InvalidArgumentException If value is not valid
     */
    public function checkValidValue(int $value): void
    {
        $this->range()->checkValidValue($value, $this);
    }

    /**
     * @param int[] $values
     *
     * @psalm-assert int[] $values
     *
     * @return ValueRange
     */
    private function createRange(array $values): ValueRange
    {
        if (count($values) >= 4) {
            return ValueRange::ofVariable(...$values);
        }
        if (count($values) >= 3) {
            return ValueRange::ofVariableMax(...$values);
        }

        return ValueRange::ofFixed(...$values);
    }
}