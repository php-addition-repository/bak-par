<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Core\Hashable;
use Par\Time\Assert;
use Par\Time\Exception\InvalidArgumentException;

/**
 * The range of valid values for a date-time field.
 *
 * All TemporalField instances have a valid range of values. For example, the ISO day-of-month runs from 1 to somewhere
 * between 28 and 31. This class captures that valid range.
 *
 * It is important to be aware of the limitations of this class. Only the minimum and maximum values are provided. It
 * is possible for there to be invalid values within the outer range. For example, a weird field may have valid values
 * of 1, 2, 4, 6, 7, thus have a range of '1 - 7', despite that fact that values 3 and 5 are invalid.
 *
 * Instances of this class are not tied to a specific field.
 *
 */
final class ValueRange implements Hashable
{
    private int $smallestMinimum;
    private int $largestMinimum;
    private int $smallestMaximum;
    private int $largestMaximum;

    /**
     * Obtains a fixed value range.
     *
     * This factory obtains a range where the minimum and maximum values are fixed. For example, the ISO month-of-year
     * always runs from 1 to 12.
     *
     * @param int $min The minimum value
     * @param int $max The maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If the minimum is greater than the maximum.
     */
    public static function ofFixed(int $min, int $max): self
    {
        return new self($min, $min, $max, $max);
    }

    /**
     * Obtains a fully variable value range.
     *
     * This factory obtains a range where both the minimum and maximum value may vary.
     *
     * @param int $smallestMin The smallest minimum value
     * @param int $largestMin  The largest minimum value
     * @param int $smallestMax The smallest maximum value
     * @param int $largestMax  The largest maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If the smallest minimum is greater than the smallest maximum, or the smallest
     * maximum is greater than the largest maximum or the largest minimum is greater than the largest maximum.
     */
    public static function ofVariable(int $smallestMin, int $largestMin, int $smallestMax, int $largestMax): self
    {
        return new self($smallestMin, $largestMin, $smallestMax, $largestMax);
    }

    /**
     * Obtains a variable value range.
     *
     * This factory obtains a range where the minimum value is fixed and the maximum value may vary. For example, the
     * ISO day-of-month always starts at 1, but ends between 28 and 31.
     *
     * @param int $min         The minimum value
     * @param int $smallestMax The smallest maximum value
     * @param int $largestMax  The largest maximum value
     *
     * @return ValueRange
     * @throws InvalidArgumentException If minimum is greater than the smallest maximum, or the smallest
     * maximum is greater than the largest maximum or the minimum is greater than the largest maximum.
     */
    public static function ofVariableMax(int $min, int $smallestMax, int $largestMax): self
    {
        return new self($min, $min, $smallestMax, $largestMax);
    }

    /**
     * @inheritDoc
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->smallestMinimum === $other->smallestMinimum
                && $this->largestMinimum === $other->largestMinimum
                && $this->smallestMaximum === $other->smallestMaximum
                && $this->largestMaximum === $other->largestMaximum;
        }

        return false;
    }

    public function hash(): string
    {
        return str_replace(' ', '', $this->toString());
    }

    /**
     * The format will be '{min}/{largestMin} - {smallestMax}/{max}', where the largestMin or smallestMax sections may
     * be omitted, together with associated slash, if they are the same as the min or max.
     *
     * @return string
     */
    public function toString(): string
    {
        $text = $this->getMinimum();
        if ($this->getMinimum() !== $this->getLargestMinimum()) {
            $text .= '/' . $this->getLargestMinimum();
        }

        $text .= ' - ' . $this->getSmallestMaximum();
        if ($this->getSmallestMaximum() !== $this->getMaximum()) {
            $text .= '/' . $this->getMaximum();
        }

        return $text;
    }

    /**
     * Gets the minimum value that the field can take.
     *
     * For example, the ISO day-of-month always starts at 1. The minimum is therefore 1.
     *
     * @return int
     */
    public function getMinimum(): int
    {
        return $this->smallestMinimum;
    }

    /**
     * Gets the largest possible minimum value that the field can take.
     *
     * For example, the ISO day-of-month always starts at 1. The largest minimum is therefore 1.
     *
     * @return int
     */
    public function getLargestMinimum(): int
    {
        return $this->largestMinimum;
    }

    /**
     * Gets the maximum value that the field can take.
     *
     * For example, the ISO day-of-month runs to between 28 and 31 days. The maximum is therefore 31.
     *
     * @return int
     */
    public function getMaximum(): int
    {
        return $this->largestMaximum;
    }

    /**
     * Gets the smallest possible maximum value that the field can take.
     *
     * For example, the ISO day-of-month runs to between 28 and 31 days. The smallest maximum is therefore 28.
     *
     * @return int
     */
    public function getSmallestMaximum(): int
    {
        return $this->smallestMaximum;
    }

    /**
     * Checks if the value is within the valid range.
     *
     * This checks that the value is within the stored range of values.
     *
     * @param int $value The value to check.
     *
     * @return bool
     */
    public function isValidValue(int $value): bool
    {
        return $value >= $this->getMinimum() && $value <= $this->getMaximum();
    }

    /**
     * Checks that the specified value is valid.
     *
     * This validates that the value is within the valid range of values
     *
     * @param int           $value The value to check
     * @param TemporalField $field The field the value comes from, only used to improve exception message
     *
     * @return int The value that was passed in.
     * @throws InvalidArgumentException If value is outside of range
     */
    public function checkValidValue(int $value, TemporalField $field): int
    {
        if ($this->isValidValue($value)) {
            return $value;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Expected a value within range %s for %s, got %d',
                $this->toString(),
                $field->toString(),
                $value
            )
        );
    }

    /**
     * @param int $smallestMinimum
     * @param int $largestMinimum
     * @param int $smallestMaximum
     * @param int $largestMaximum
     */
    private function __construct(int $smallestMinimum, int $largestMinimum, int $smallestMaximum, int $largestMaximum)
    {
        Assert::lessThanEq($smallestMinimum, $largestMinimum);
        Assert::lessThan($largestMinimum, $smallestMaximum);
        Assert::lessThanEq($largestMinimum, $largestMaximum);

        $this->smallestMinimum = min($smallestMinimum, $largestMinimum);
        $this->largestMinimum = max($largestMinimum, $smallestMinimum);
        $this->smallestMaximum = min($smallestMaximum, $largestMaximum);
        $this->largestMaximum = max($largestMaximum, $smallestMaximum);
    }
}