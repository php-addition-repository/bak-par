<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\LocalDate;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Factory;
use Par\Time\LocalDate;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use PHPUnit\Framework\TestCase;

final class CreateTest extends TestCase
{
    use TimeTestCaseTrait;
    use HashableAssertions;

    public function testItCanBeObtainedOfYearMonthAndDayOfMonth(): void
    {
        $year = 2014;
        $month = 1;
        $dayOfMonth = 12;

        $result = LocalDate::of($year, $month, $dayOfMonth);

        self::assertSame($year, $result->yearValue());
        self::assertSame($month, $result->monthValue());
        self::assertSame($dayOfMonth, $result->dayOfMonth());
    }

    public function testItCanBeObtainedForCurrentDate(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $result = LocalDate::now();

                self::assertSameYear($now, $result->yearValue());
                self::assertSameMonth($now, $result->monthValue());
                self::assertSameDay($now, $result->dayOfMonth());
            }
        );
    }

    public function testItCanBeObtainedForTodaysDate(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::today();

                $result = LocalDate::today();

                self::assertSameYear($now, $result->yearValue());
                self::assertSameMonth($now, $result->monthValue());
                self::assertSameDay($now, $result->dayOfMonth());
            }
        );
    }

    public function testItCanBeObtainedForYesterdaysDate(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::yesterday();

                $result = LocalDate::yesterday();

                self::assertSameYear($now, $result->yearValue());
                self::assertSameMonth($now, $result->monthValue());
                self::assertSameDay($now, $result->dayOfMonth());
            }
        );
    }

    public function testItCanBeObtainedForTomorrowsDate(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::tomorrow();

                $result = LocalDate::tomorrow();

                self::assertSameYear($now, $result->yearValue());
                self::assertSameMonth($now, $result->monthValue());
                self::assertSameDay($now, $result->dayOfMonth());
            }
        );
    }

    public function testItCanBeObtainedUsingYearAndDaysOfYear(): void
    {
        $year = 2014;
        $dayOfYear = 122;

        $result = LocalDate::ofYearDay($year, $dayOfYear);

        self::assertSame($year, $result->yearValue());
        self::assertSame($dayOfYear, $result->dayOfYear());
        self::assertSame(5, $result->monthValue());
        self::assertSame(2, $result->dayOfMonth());
    }

    /**
     * @dataProvider provideValidText
     *
     * @param string $text
     * @param int    $expectedYear
     * @param int    $expectedMonth
     * @param int    $expectedDayOfMonth
     *
     * @return void
     */
    public function testItCanBeObtainedUsingText(string $text,
                                                 int $expectedYear,
                                                 int $expectedMonth,
                                                 int $expectedDayOfMonth): void
    {
        $result = LocalDate::parse($text);

        self::assertSame($expectedYear, $result->yearValue());
        self::assertSame($expectedMonth, $result->monthValue());
        self::assertSame($expectedDayOfMonth, $result->dayOfMonth());
    }

    /**
     * @dataProvider provideInvalidText
     *
     * @param string $invalidText
     *
     * @return void
     */
    public function testItWillThrowExceptionWhenParsingInvalidText(string $invalidText): void
    {
        $this->expectException(InvalidArgumentException::class);

        LocalDate::parse($invalidText);
    }

    /**
     * @dataProvider provideNative
     *
     * @param DateTimeInterface $dateTime
     *
     * @return void
     */
    public function testItCanBeObtainedForNativeDate(DateTimeInterface $dateTime): void
    {
        $result = LocalDate::fromNative($dateTime);

        self::assertSameYear($dateTime, $result->yearValue());
        self::assertSameMonth($dateTime, $result->monthValue());
        self::assertSameDay($dateTime, $result->dayOfMonth());
    }

    /**
     * @return array<string, array{DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                Factory::createDate(2021, 1, 20),
            ],
        ];
    }

    /**
     * @return array<string, array{string, int, int, int}>
     */
    public function provideValidText(): array
    {
        return [
            'simple-date' => ['2010-01-01', 2010, 1, 1],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideInvalidText(): array
    {
        return [
            'no-date' => ['foobar'],
            'invalid-iso-year' => ['10-10-10'],
            'invalid-iso-month' => ['2010-1-10'],
            'invalid-iso-day' => ['2010-10-1'],
            'non-existing-date' => ['2010-02-30'],
        ];
    }
}