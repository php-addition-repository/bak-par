<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Factory;
use Par\Time\Month;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use Par\Time\Year;
use Par\Time\YearMonth;
use PHPUnit\Framework\TestCase;

class YearMonthTest extends TestCase
{
    use TimeTestCaseTrait;
    use HashableAssertions;

    /**
     * @test
     */
    public function itCanBeCreatedFromValue(): void
    {
        $expectedYear = 2011;
        $expectedMonth = 7;
        $monthDay = YearMonth::of($expectedYear, $expectedMonth);

        self::assertSame($expectedYear, $monthDay->yearValue());
        self::assertSame($expectedMonth, $monthDay->monthValue());
    }

    /**
     * @test
     */
    public function itCanBeTransformedToString(): void
    {
        self::assertSame(YearMonth::of(2020, 1)->toString(), '2020-01');
    }

    /**
     * @test
     */
    public function itCanDetermineEqualityWithOther(): void
    {
        $yearMonth = YearMonth::of(2020, 1);
        $otherYearMonth = YearMonth::of(2021, 2);

        self::assertTrue($yearMonth->equals(clone $yearMonth));
        self::assertFalse($yearMonth->equals($otherYearMonth));
        self::assertFalse($yearMonth->equals(null));
    }

    /**
     * @test
     */
    public function itCanUpdateMonth(): void
    {
        $yearMonth = YearMonth::of(2020, 1);

        self::assertHashEquals(
            Month::of(4),
            $yearMonth->withMonth(Month::of(4))->month()
        );
        self::assertHashEquals($yearMonth->year(), $yearMonth->withMonth(Month::of(4))->year());
        self::assertHashNotEquals($yearMonth, $yearMonth->withMonth(Month::of(4)));

        self::assertHashEquals(Month::of(4), $yearMonth->withMonth(4)->month());
        self::assertHashEquals($yearMonth->year(), $yearMonth->withMonth(4)->year());
        self::assertHashNotEquals($yearMonth, $yearMonth->withMonth(4));
    }

    /**
     * @test
     */
    public function itCanUpdateYear(): void
    {
        $yearMonth = YearMonth::of(2020, 1);

        self::assertHashEquals(
            Year::of(1995),
            $yearMonth->withYear(Year::of(1995))->year()
        );
        self::assertHashEquals($yearMonth->month(), $yearMonth->withYear(Year::of(1995))->month());
        self::assertHashNotEquals($yearMonth, $yearMonth->withYear(Year::of(1995)));

        self::assertHashEquals(Year::of(1995), $yearMonth->withYear(1995)->year());
        self::assertHashEquals($yearMonth->month(), $yearMonth->withYear(1995)->month());
        self::assertHashNotEquals($yearMonth, $yearMonth->withYear(1995));
    }

    /**
     * @test
     */
    public function itIsHashable(): void
    {
        self::assertSame(200103, YearMonth::of(2001, 3)->hash());
    }

    /**
     * @return array<string, array{YearMonth, DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                YearMonth::of(2021, 1),
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                YearMonth::of(2019, 7),
                Factory::createDate(2019, 7, 20),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideNative
     *
     * @param YearMonth         $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function itCanBeCreatedFromNativeDateTime(YearMonth $expected, DateTimeInterface $native): void
    {
        self::assertHashEquals($expected, YearMonth::fromNative($native));
    }

    /**
     * @test
     */
    public function itCanBeCreatedForCurrentYear(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $current = YearMonth::now();

                self::assertSameYear($now, $current->yearValue());
                self::assertSameMonth($now, $current->monthValue());
            }
        );
    }

    /**
     * @return array<string, array{string, YearMonth}>
     */
    public function provideStrings(): array
    {
        return [
            'month < 10' => ['2001-02', YearMonth::of(2001, 2)],
            'month > 10' => ['2001-12', YearMonth::of(2001, 12)],
            'large year' => ['999999-01', YearMonth::of(999999, 1)],
            'small year' => ['1-01', YearMonth::of(1, 1)],
            'zero year' => ['0-01', YearMonth::of(0, 1)],
            'negative year' => ['-2-01', YearMonth::of(-2, 1)],
        ];
    }

    /**
     * @dataProvider provideStrings
     *
     * @param string    $text
     * @param YearMonth $expected
     *
     * @test
     */
    public function itCanBeCreatedFromString(string $text, YearMonth $expected): void
    {
        self::assertHashEquals($expected, YearMonth::parse($text));
    }

    /**
     * @test
     */
    public function ItWillThrowExceptionWhenCreatingFromInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        YearMonth::parse('The year 2000');
    }

    /**
     * @test
     */
    public function itCanBeNaturallySorted(): void
    {
        $list = [
            YearMonth::of(2001, 3),
            YearMonth::of(2010, 1),
            YearMonth::of(2001, 2),
        ];

        uasort(
            $list,
            static function (YearMonth $a, YearMonth $b): int {
                return $a->compareTo($b);
            }
        );

        $orderedList = [];
        foreach ($list as $item) {
            $orderedList[] = $item->toString();
        }

        self::assertSame(
            ['2001-02', '2001-03', '2010-01'],
            $orderedList
        );
    }
}