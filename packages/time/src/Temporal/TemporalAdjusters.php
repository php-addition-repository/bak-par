<?php

namespace Par\Time\Temporal;

use Par\Time\Chrono\ChronoField;
use Par\Time\DayOfWeek;
use Par\Time\Exception\UnsupportedTemporalType;

/**
 * Common and useful TemporalAdjusters.
 *
 * Adjusters are a key tool for modifying temporal objects. They exist to externalize the process of adjustment,
 * permitting different approaches, as per the strategy design pattern. Examples might be an adjuster that sets the
 * date avoiding weekends, or one that sets the date to the last day of the month.
 */
final class TemporalAdjusters
{
    /**
     * Adjust a Temporal using its toNative/fromNative construct
     *
     * @param string   $modification
     * @param Temporal $temporal
     *
     * @return Temporal
     * @internal
     *
     * @template       T of Temporal
     * @psalm-param T  $temporal
     * @psalm-return T
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function modifyViaNative(string $modification, Temporal $temporal): Temporal
    {
        $native = $temporal->toNative();
        $modified = $native->modify($modification);

        return forward_static_call([get_class($temporal), 'fromNative'], $modified);
    }

    /**
     * Returns the next day-of-week adjuster, which adjusts the date to the first occurrence of the specified
     * day-of-week after the date being adjusted.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 (a Saturday) for parameter (MONDAY) will return 2011-01-17 (two days later).
     * The input 2011-01-15 (a Saturday) for parameter (WEDNESDAY) will return 2011-01-19 (four days later).
     * The input 2011-01-15 (a Saturday) for parameter (SATURDAY) will return 2011-01-22 (seven days later).
     *
     * The behavior is suitable for use with most calendar systems. It uses the DAY_OF_WEEK field and the DAYS unit,
     * and assumes a seven day week.
     *
     * @param DayOfWeek $dayOfWeek The day-of-week to move the date to
     *
     * @return TemporalAdjuster The next day-of-week adjuster
     */
    public static function next(DayOfWeek $dayOfWeek): TemporalAdjuster
    {
        return self::moveDayOfWeek($dayOfWeek, 'next');
    }

    /**
     * Returns the next-or-same day-of-week adjuster, which adjusts the date to the first occurrence of the specified
     * day-of-week after the date being adjusted unless it is already on that day in which case the same object is
     * returned.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 (a Saturday) for parameter (MONDAY) will return 2011-01-17 (two days later).
     * The input 2011-01-15 (a Saturday) for parameter (WEDNESDAY) will return 2011-01-19 (four days later).
     * The input 2011-01-15 (a Saturday) for parameter (SATURDAY) will return 2011-01-15 (same as input).
     *
     * The behavior is suitable for use with most calendar systems. It uses the DAY_OF_WEEK field and the DAYS unit,
     * and assumes a seven day week.
     *
     * @param DayOfWeek $dayOfWeek The day-of-week to check for or move the date to
     *
     * @return TemporalAdjuster The next-or-same day-of-week adjuster
     */
    public static function nextOrSame(DayOfWeek $dayOfWeek): TemporalAdjuster
    {
        return self::moveDayOfWeek($dayOfWeek, 'next', true);
    }

    /**
     * Returns the previous day-of-week adjuster, which adjusts the date to the first occurrence of the specified
     * day-of-week before the date being adjusted.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 (a Saturday) for parameter (MONDAY) will return 2011-01-10 (five days earlier).
     * The input 2011-01-15 (a Saturday) for parameter (WEDNESDAY) will return 2011-01-12 (three days earlier).
     * The input 2011-01-15 (a Saturday) for parameter (SATURDAY) will return 2011-01-08 (seven days earlier).
     *
     * The behavior is suitable for use with most calendar systems. It uses the DAY_OF_WEEK field and the DAYS unit,
     * and assumes a seven day week.
     *
     * @param DayOfWeek $dayOfWeek The day-of-week to move the date to
     *
     * @return TemporalAdjuster The previous day-of-week adjuster
     */
    public static function previous(DayOfWeek $dayOfWeek): TemporalAdjuster
    {
        return self::moveDayOfWeek($dayOfWeek, 'previous');
    }

    /**
     * Returns the previous-or-same day-of-week adjuster, which adjusts the date to the first occurrence of the
     * specified day-of-week before the date being adjusted unless it is already on that day in which case the same
     * object is returned.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 (a Saturday) for parameter (MONDAY) will return 2011-01-10 (five days earlier).
     * The input 2011-01-15 (a Saturday) for parameter (WEDNESDAY) will return 2011-01-12 (three days earlier).
     * The input 2011-01-15 (a Saturday) for parameter (SATURDAY) will return 2011-01-15 (same as input).
     *
     * The behavior is suitable for use with most calendar systems. It uses the DAY_OF_WEEK field and the DAYS unit,
     * and assumes a seven day week.
     *
     * @param DayOfWeek $dayOfWeek The day-of-week to check for or move the date to
     *
     * @return TemporalAdjuster The previous-or-same day-of-week adjuster
     */
    public static function previousOrSame(DayOfWeek $dayOfWeek): TemporalAdjuster
    {
        return self::moveDayOfWeek($dayOfWeek, 'previous', true);
    }

    /**
     * @param int          $amount
     * @param TemporalUnit $unit
     *
     * @return TemporalAdjuster
     * @internal
     */
    public static function plusUnit(int $amount, TemporalUnit $unit): TemporalAdjuster
    {
        return new class($amount, $unit) implements TemporalAdjuster {

            public function __construct(private int $amount, private TemporalUnit $unit)
            {
            }

            /**
             * @inheritDoc
             *
             * @psalm-suppress MixedInferredReturnType
             * @psalm-suppress MixedReturnStatement
             */
            public function adjustInto(Temporal $temporal): Temporal
            {
                $unit = $this->unit;
                if (!$temporal->supportsUnit($unit)) {
                    throw UnsupportedTemporalType::forUnit($unit);
                }

                if ($this->amount === 0) {
                    return $temporal;
                }

                $modification = $unit->toNativeModifier($this->amount);

                return TemporalAdjusters::modifyViaNative($modification, $temporal);
            }

        };
    }

    /**
     * Returns the "first day of month" adjuster, which returns a new date set to the first day of the current month.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 will return 2011-01-01.
     * The input 2011-02-15 will return 2011-02-01.
     *
     * @return TemporalAdjuster The first day-of-month adjuster
     */
    public static function firstDayOfMonth(): TemporalAdjuster
    {
        return new class() implements TemporalAdjuster {
            /**
             * @inheritDoc
             */
            public function adjustInto(Temporal $temporal): Temporal
            {
                return $temporal->withField(ChronoField::DayOfMonth(), 1);
            }
        };
    }

    /**
     * Returns the "last day of month" adjuster, which returns a new date set to the last day of the current month.
     *
     * The ISO calendar system behaves as follows:
     * The input 2011-01-15 will return 2011-01-31.
     * The input 2011-02-15 will return 2011-02-28.
     * The input 2012-02-15 will return 2012-02-29 (leap year).
     * The input 2011-04-15 will return 2011-04-30.
     *
     * @return TemporalAdjuster The first day-of-month adjuster
     */
    public static function lastDayOfMonth(): TemporalAdjuster
    {
        return new class() implements TemporalAdjuster {
            /**
             * @inheritDoc
             */
            public function adjustInto(Temporal $temporal): Temporal
            {
                $field = ChronoField::DayOfMonth();
                if (!$temporal->supportsField($field)) {
                    throw UnsupportedTemporalType::forField($field);
                }

                $modification = 'last day of this month';

                return TemporalAdjusters::modifyViaNative($modification, $temporal);
            }
        };
    }

    /**
     * @param DayOfWeek $dayOfWeek
     * @param string    $nextOrPrev
     * @param bool      $keepIfSame
     *
     * @return TemporalAdjuster
     */
    private static function moveDayOfWeek(DayOfWeek $dayOfWeek,
                                          string $nextOrPrev,
                                          bool $keepIfSame = false): TemporalAdjuster
    {
        return new class($dayOfWeek, $nextOrPrev, $keepIfSame) implements TemporalAdjuster {

            public function __construct(private DayOfWeek $dayOfWeek,
                                        private string $nextOrPrev,
                                        private bool $keepIfSame = false)
            {
            }

            /**
             * @inheritDoc
             */
            public function adjustInto(Temporal $temporal): Temporal
            {
                $field = ChronoField::DayOfWeek();
                $unit = $field->getBaseUnit();
                if (!$temporal->supportsUnit($unit)) {
                    throw UnsupportedTemporalType::forUnit($unit);
                }

                if ($this->keepIfSame) {
                    $currentValue = $temporal->get($field);
                    $current = DayOfWeek::of($currentValue);
                    if ($this->dayOfWeek->equals($current)) {
                        return $temporal;
                    }
                }

                $modification = sprintf('%s %s', $this->nextOrPrev, $this->dayOfWeek->toString());

                return TemporalAdjusters::modifyViaNative($modification, $temporal);
            }

        };
    }
}