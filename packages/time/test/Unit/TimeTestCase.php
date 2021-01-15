<?php

declare(strict_types=1);

namespace ParTest\Time\Unit;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use Par\Time\Factory;
use PHPUnit\Framework\TestCase;

abstract class TimeTestCase extends TestCase
{
    private string $saveTz;

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $hour
     * @param int|string               $minute
     * @param int|string               $second
     *
     * @psalm-param int|numeric-string $hour
     * @psalm-param int|numeric-string $minute
     * @psalm-param int|numeric-string $second
     *
     * @return void
     */
    protected static function assertSameTime(DateTimeInterface $actualDateTime,
                                             int|string $hour,
                                             int|string $minute,
                                             int|string $second): void
    {
        self::assertSameHour($actualDateTime, $hour);
        self::assertSameMinute($actualDateTime, $minute);
        self::assertSameSecond($actualDateTime, $second);
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $hour
     *
     * @psalm-param int|numeric-string $hour
     *
     * @return void
     */
    protected static function assertSameHour(DateTimeInterface $actualDateTime, int|string $hour): void
    {
        self::assertSame((int)$hour, (int)$actualDateTime->format('H'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $minute
     *
     * @psalm-param int|numeric-string $minute
     *
     * @return void
     */
    protected static function assertSameMinute(DateTimeInterface $actualDateTime, int|string $minute): void
    {
        self::assertSame((int)$minute, (int)$actualDateTime->format('i'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $second
     *
     * @psalm-param int|numeric-string $second
     *
     * @return void
     */
    protected static function assertSameSecond(DateTimeInterface $actualDateTime, int|string $second): void
    {
        self::assertSame((int)$second, (int)$actualDateTime->format('s'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $year
     * @param int|string               $month
     * @param int|string               $day
     *
     * @psalm-param int|numeric-string $year
     * @psalm-param int|numeric-string $month
     * @psalm-param int|numeric-string $day
     *
     * @return void
     */
    protected static function assertSameDate(DateTimeInterface $actualDateTime,
                                             int|string $year,
                                             int|string $month,
                                             int|string $day): void
    {
        self::assertSameYear($actualDateTime, $year);
        self::assertSameMonth($actualDateTime, $month);
        self::assertSameDay($actualDateTime, $day);
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $year
     *
     * @psalm-param int|numeric-string $year
     *
     * @return void
     */
    protected static function assertSameYear(DateTimeInterface $actualDateTime, int|string $year): void
    {
        self::assertSame((int)$year, (int)$actualDateTime->format('Y'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $month
     *
     * @psalm-param int|numeric-string $month
     *
     * @return void
     */
    protected static function assertSameMonth(DateTimeInterface $actualDateTime, int|string $month): void
    {
        self::assertSame((int)$month, (int)$actualDateTime->format('n'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $day
     *
     * @psalm-param int|numeric-string $day
     *
     * @return void
     */
    protected static function assertSameDay(DateTimeInterface $actualDateTime, int|string $day): void
    {
        self::assertSame((int)$day, (int)$actualDateTime->format('d'));
    }

    /**
     * @param DateTimeInterface        $actualDateTime
     * @param int|string               $year
     * @param int|string               $month
     * @param int|string               $day
     * @param int|string               $hour
     * @param int|string               $minute
     * @param int|string               $second
     *
     * @psalm-param int|numeric-string $year
     * @psalm-param int|numeric-string $month
     * @psalm-param int|numeric-string $day
     * @psalm-param int|numeric-string $hour
     * @psalm-param int|numeric-string $minute
     * @psalm-param int|numeric-string $second
     *
     * @return void
     */
    protected static function assertDateTime(DateTimeInterface $actualDateTime,
                                             int|string $year,
                                             int|string $month,
                                             int|string $day,
                                             int|string $hour,
                                             int|string $minute,
                                             int|string $second): void
    {
        self::assertSameYear($actualDateTime, $year);
        self::assertSameMonth($actualDateTime, $month);
        self::assertSameDay($actualDateTime, $day);
        self::assertSameHour($actualDateTime, $hour);
        self::assertSameMinute($actualDateTime, $minute);
        self::assertSameSecond($actualDateTime, $second);
    }

    protected function setUp(): void
    {
        //save current timezone
        $this->saveTz = date_default_timezone_get();
        date_default_timezone_set('Europe/Amsterdam');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->saveTz);
        Factory::setTestNow();

        parent::tearDown();
    }

    /**
     * Lock Factory::create() within the Closure to make sure every call uses the exact datetime regardless of current
     * time
     *
     * @param Closure                $func The function to execute with locked now
     * @param DateTimeImmutable|null $dt   The now to use
     */
    protected function wrapWithTestNow(Closure $func, DateTimeImmutable $dt = null): void
    {
        Factory::setTestNow($dt ?? Factory::now());
        $func();
        Factory::setTestNow();
    }
}