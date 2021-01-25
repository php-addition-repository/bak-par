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
 * @method static self Micros()
 * @method static self Millis()
 * @method static self Seconds()
 * @method static self Minutes()
 * @method static self Hours()
 * @method static self HalfDays()
 * @method static self Days()
 * @method static self Weeks()
 * @method static self Months()
 * @method static self Years()
 * @method static self Decades()
 * @method static self Centuries()
 * @method static self Millennia()
 * @method static self Forever()
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

    private const HALF_DAY_IN_HOURS = self::DAY_IN_HOURS / 2;
    private const HOUR_IN_SECONDS = self::MINUTE_IN_SECONDS * self::HOUR_IN_MINUTES;
    private const DAY_IN_MINUTES = self::DAY_IN_HOURS * self::HOUR_IN_MINUTES;
    private const DAY_IN_SECONDS = self::DAY_IN_MINUTES * self::MINUTE_IN_SECONDS;


    /**
     * @inheritDoc
     */
    public function isDateBased(): bool
    {
        return match ($this) {
            self::Micros(), self::Millis(), self::Seconds(), self::Minutes(), self::Hours(), self::HalfDays(
            ), self::Forever() => false,
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
            self::Micros(), self::Millis(), self::Seconds(), self::Minutes(), self::Hours(), self::HalfDays() => true,
            default => false,
        };
    }

    public function toNativeModifier(int $amount): string
    {
        [$unit, $amount] = match ($this) {
            ChronoUnit::Forever() => [$this, 0],
            ChronoUnit::Millennia() => [ChronoUnit::Years(), $amount * self::MILLENNIUM_IN_YEARS],
            ChronoUnit::Centuries() => [ChronoUnit::Years(), $amount * self::CENTURY_IN_YEARS],
            ChronoUnit::Decades() => [ChronoUnit::Years(), $amount * self::DECADE_IN_YEARS],
            ChronoUnit::HalfDays() => [ChronoUnit::Hours(), $amount * self::HALF_DAY_IN_HOURS],
            default => [$this, $amount],
        };

        $sign = '';
        if ($amount >= -1) {
            $sign = '+';
        }
        return sprintf('%s%d %s', $sign, $amount, $unit->toString());
    }

}