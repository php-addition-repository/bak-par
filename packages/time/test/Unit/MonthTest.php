<?php

declare(strict_types=1);

namespace ParTest\Time\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Factory;
use Par\Time\Month;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use ParTest\Core\Unit\PHPUnit\EnumTestCaseTrait;
use PHPUnit\Framework\TestCase;

class MonthTest extends TestCase
{
    /**
     * @test
     */
    public function itWillReturnValue(): void
    {
        self::assertSame(2, Month::February()->value());
    }

    use HashableAssertions;
    use EnumTestCaseTrait {
        EnumTestCaseTrait::setUp as enumSetup;
    }

    use TimeTestCaseTrait {
        TimeTestCaseTrait::setUp as timeSetup;
    }

    /**
     * @test
     */
    public function itCanBeCreatedFromValue(): void
    {
        $expected = Month::March();

        self::assertHashEquals($expected, Month::of(3));
    }

    /**
     * @test
     */
    public function itWillThrowInvalidArgumentExceptionWhenCreateFromOutOfRangeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Month::of(13);
    }

    /**
     * @return array<string, array{Month, DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                Month::January(),
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                Month::July(),
                Factory::createDate(2019, 7, 20),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideNative
     *
     * @param Month             $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function itCanBeCreatedFromNativeDateTime(Month $expected, DateTimeInterface $native): void
    {
        self::assertSame($expected, Month::fromNative($native));
    }

    /**
     * @test
     */
    public function itWillContainAllDaysOfWeek(): void
    {
        self::assertSame(
            [
                Month::January(),
                Month::February(),
                Month::March(),
                Month::April(),
                Month::May(),
                Month::June(),
                Month::July(),
                Month::August(),
                Month::September(),
                Month::October(),
                Month::November(),
                Month::December(),
            ],
            Month::values()
        );
    }

    /**
     * @test
     */
    public function itCanBeMovedViaAddition(): void
    {
        self::assertSame(Month::February(), Month::January()->plus(1));
        self::assertSame(Month::March(), Month::January()->plus(14));
        self::assertSame(Month::November(), Month::January()->plus(-2));
        self::assertSame(Month::January(), Month::January()->plus(0));
        self::assertSame(Month::March(), Month::January()->plus(14));
    }

    /**
     * @test
     */
    public function itCanBeMovedViaSubtraction(): void
    {
        self::assertSame(Month::December(), Month::January()->minus(1));
        self::assertSame(Month::April(), Month::January()->minus(9));
        self::assertSame(Month::March(), Month::January()->minus(-2));
        self::assertSame(Month::January(), Month::January()->minus(0));
        self::assertSame(Month::November(), Month::January()->minus(14));
    }

    /**
     * @return array<string, array{int, Month, bool}>
     */
    public function provideMonthLengths(): array
    {
        return [
            'January' => [31, Month::January(), false],
            'February' => [28, Month::February(), false],
            'February in leap year' => [29, Month::February(), true],
            'April' => [30, Month::April(), true],
            'May' => [31, Month::May(), true],
            'June' => [30, Month::June(), true],
            'July' => [31, Month::July(), true],
            'August' => [31, Month::August(), true],
            'September' => [30, Month::September(), true],
            'October' => [31, Month::October(), true],
            'November' => [30, Month::November(), true],
            'December' => [31, Month::December(), true],
        ];
    }

    /**
     * @test
     * @dataProvider provideMonthLengths
     *
     * @param int   $expected
     * @param Month $month
     * @param bool  $leapYear
     */
    public function itCanReturnLengthInDays(int $expected, Month $month, bool $leapYear): void
    {
        self::assertSame($expected, $month->length($leapYear));
    }

    /**
     * @test
     */
    public function itCanReturnTheFirstMonthOfTheQuarterOfProvidedMonth(): void
    {
        self::assertSame(Month::January(), Month::January()->firstMonthOfQuarter());
        self::assertSame(Month::January(), Month::February()->firstMonthOfQuarter());
        self::assertSame(Month::January(), Month::March()->firstMonthOfQuarter());
        self::assertSame(Month::April(), Month::April()->firstMonthOfQuarter());
        self::assertSame(Month::April(), Month::May()->firstMonthOfQuarter());
        self::assertSame(Month::April(), Month::June()->firstMonthOfQuarter());
        self::assertSame(Month::July(), Month::July()->firstMonthOfQuarter());
        self::assertSame(Month::July(), Month::August()->firstMonthOfQuarter());
        self::assertSame(Month::July(), Month::September()->firstMonthOfQuarter());
        self::assertSame(Month::October(), Month::October()->firstMonthOfQuarter());
        self::assertSame(Month::October(), Month::November()->firstMonthOfQuarter());
        self::assertSame(Month::October(), Month::December()->firstMonthOfQuarter());
    }

    /**
     * @test
     */
    public function itCanReturnTheMonthForToday(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(Month::January(), Month::today());
            },
            Factory::createDate(2021, 1, 1)
        );
    }

    /**
     * @test
     */
    public function itCanReturnTheMonthForTomorrow(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(Month::February(), Month::tomorrow());
            },
            Factory::createDate(2021, 1, 31)
        );
    }

    /**
     * @test
     */
    public function itCanReturnTheMonthForYesterday(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(Month::December(), Month::yesterday());
            },
            Factory::createDate(2021, 1, 1)
        );
    }

    /**
     * @return array<string, array{Month, bool, int}>
     */
    public function provideFirstDayOfYearValues(): array
    {
        return [
            'January' => [Month::January(), false, 1],
            'January - leap' => [Month::January(), true, 1],
            'February' => [Month::February(), false, 32],
            'February - leap' => [Month::February(), true, 32],
            'March' => [Month::March(), false, 60],
            'March - leap' => [Month::March(), true, 61],
            'April' => [Month::April(), false, 91],
            'April - leap' => [Month::April(), true, 92],
            'May' => [Month::May(), false, 121],
            'May - leap' => [Month::May(), true, 122],
            'June' => [Month::June(), false, 152],
            'June - leap' => [Month::June(), true, 153],
            'July' => [Month::July(), false, 182],
            'July - leap' => [Month::July(), true, 183],
            'August' => [Month::August(), false, 213],
            'August - leap' => [Month::August(), true, 214],
            'September' => [Month::September(), false, 244],
            'September - leap' => [Month::September(), true, 245],
            'October' => [Month::October(), false, 274],
            'October - leap' => [Month::October(), true, 275],
            'November' => [Month::November(), false, 305],
            'November - leap' => [Month::November(), true, 306],
            'December' => [Month::December(), false, 335],
            'December - leap' => [Month::December(), true, 336],
        ];
    }

    /**
     * @test
     * @dataProvider provideFirstDayOfYearValues
     *
     * @param Month $month
     * @param bool  $leapYear
     * @param int   $expected
     */
    public function itCanDetermineAmountOfDaysInYearUntilFirstDayOfMonth(Month $month,
                                                                         bool $leapYear,
                                                                         int $expected): void
    {
        self::assertSame($expected, $month->firstDayOfYear($leapYear));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->enumSetup();
        $this->timeSetup();
    }
}