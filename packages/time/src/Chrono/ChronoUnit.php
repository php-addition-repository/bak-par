<?php

declare(strict_types=1);

namespace Par\Time\Chrono;

use Par\Core\Enum;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalUnit;

/**
 * @psalm-immutable
 * @extends Enum<ChronoUnit>
 *
 * @method static self MICROS()
 * @method static self MILLIS()
 * @method static self SECONDS()
 * @method static self MINUTES()
 * @method static self HOURS()
 * @method static self HALF_DAYS()
 * @method static self DAYS()
 * @method static self WEEKS()
 * @method static self MONTHS()
 * @method static self YEARS()
 * @method static self DECADES()
 * @method static self CENTURIES()
 * @method static self MILLENNIA()
 * @method static self FOREVER()
 */
final class ChronoUnit extends Enum implements TemporalUnit
{
    private const MILLI_IN_MICROS = 1000;
    private const MINUTE_IN_SECONDS = 60;
    private const HOUR_IN_MINUTES = 60;
    private const DAY_IN_HOURS = 24;
    private const WEEK_IN_DAYS = 7;
    private const YEAR_IN_DAYS = 365;
    private const YEAR_IN_MONTHS = 12;
    private const DECADE_IN_YEARS = 10;
    private const CENTURY_IN_YEARS = 100;
    private const MILLENNIUM_IN_YEARS = 1000;

    private const HOUR_IN_SECONDS = self::MINUTE_IN_SECONDS * self::HOUR_IN_MINUTES;
    private const DAY_IN_MINUTES = self::DAY_IN_HOURS * self::HOUR_IN_MINUTES;
    private const DAY_IN_SECONDS = self::DAY_IN_MINUTES * self::MINUTE_IN_SECONDS;

    /**
     * @inheritDoc
     */
    public function isDateBased(): bool
    {
        return match ($this) {
            self::MICROS(), self::MILLIS(), self::SECONDS(), self::MINUTES(), self::HOURS(), self::HALF_DAYS(
            ), self::FOREVER() => false,
            default => true,
        };
    }

    /**
     * @inheritDoc
     */
    public function isDurationEstimated(): bool
    {
        return $this->isDateBased();
    }

    /**
     * @inheritDoc
     */
    public function isSupportedBy(Temporal $temporal): bool
    {
        return $temporal->supportsUnit($this);
    }

    /**
     * @inheritDoc
     */
    public function isTimeBased(): bool
    {
        return match ($this) {
            self::MICROS(), self::MILLIS(), self::SECONDS(), self::MINUTES(), self::HOURS(), self::HALF_DAYS() => true,
            default => false,
        };
    }

}