<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoField;
use Par\Time\Chrono\ChronoUnit;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Factory;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use Par\Time\Temporal\TemporalAmount;
use Par\Time\Temporal\TemporalField;
use Par\Time\Year;
use Par\Time\YearMonth;
use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    use TimeTestCaseTrait;
    use HashableAssertions;

    /**
     * @return array<string, array{string, Year}>
     */
    public function provideStrings(): array
    {
        return [
            'Numeric string' => ['2000', Year::of(2000)],
            'Negative numeric string' => ['-2000', Year::of(-2000)],
            'Positive numeric string' => ['+2000', Year::of(2000)],
            'Numeric string with zeros' => ['0000', Year::of(0)],
        ];
    }

    public function testItCanBeCreatedFromValue(): void
    {
        $expected = 2019;
        $year = Year::of($expected);

        self::assertSame($expected, $year->value());
    }

    public function testItCanBeCreatedForCurrentYear(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $currentYear = Year::now();

                self::assertSameYear($now, $currentYear->value());
            }
        );
    }

    /**
     * @dataProvider provideStrings
     *
     * @param string $text
     * @param Year   $expectedYear
     */
    public function testItCanBeCreatedFromString(string $text, Year $expectedYear): void
    {
        self::assertHashEquals($expectedYear, Year::parse($text));
    }

    /**
     * @return array<string, array{Year, DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                Year::of(2021),
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                Year::of(2019),
                Factory::createDate(2019, 7, 20),
            ],
        ];
    }

    /**
     * @dataProvider provideNative
     *
     * @param Year              $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function testItCanBeCreatedFromNativeDateTime(Year $expected, DateTimeInterface $native): void
    {
        self::assertHashEquals($expected, Year::fromNative($native));
    }

    public function testItCanDetermineEqualityWithOther(): void
    {
        $year = Year::of(2001);
        $otherYear = Year::of(2012);

        self::assertTrue($year->equals($year));
        self::assertFalse($year->equals($otherYear));
        self::assertFalse($year->equals(null));
    }

    public function testItWillReturnValue(): void
    {
        self::assertSame(2001, Year::of(2001)->value());
    }

    public function testItCanDetermineIfYearIsLeapYear(): void
    {
        self::assertTrue(Year::of(1904)->isLeap()); // divisible by 4
        self::assertTrue(Year::of(2000)->isLeap()); // divisible by 400

        self::assertFalse(Year::of(1900)->isLeap()); // divisible by 100
        self::assertFalse(Year::of(1901)->isLeap()); // not divisible by 4
    }

    public function testItCanProvideTheLengthInDays(): void
    {
        self::assertSame(365, Year::of(1995)->length());
        self::assertSame(366, Year::of(2000)->length());
    }

    public function testItCanBeTransformedToString(): void
    {
        self::assertSame(Year::of(2012)->toString(), '2012');
    }

    public function testItWillThrowExceptionWhenCreatingFromInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::parse('The year 2000');
    }

    public function testItWillThrowExceptionWhenCreatingFromIntegerGreaterThanMax(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(PHP_INT_MAX);
    }

    public function testItWillThrowExceptionWhenCreatingFromIntegerLessThanMin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Year::of(PHP_INT_MIN);
    }

    public function testItCanDetermineIfIntegerIsLeapYear(): void
    {
        self::assertTrue(Year::isLeapYear(1904)); // divisible by 4
        self::assertTrue(Year::isLeapYear(2000)); // divisible by 400

        self::assertFalse(Year::isLeapYear(1900)); // divisible by 100
        self::assertFalse(Year::isLeapYear(1901)); // not divisible by 4

        self::assertFalse(Year::isLeapYear(0)); // not divisible at all
    }

    public function testItCanBeNaturallySorted(): void
    {
        $list = [
            Year::of(2001),
            Year::of(2010),
            Year::of(1999),
        ];

        uasort(
            $list,
            static function (Year $a, Year $b): int {
                return $a->compareTo($b);
            }
        );

        $orderedList = [];
        foreach ($list as $year) {
            $orderedList[] = $year->value();
        }

        self::assertSame(
            [1999, 2001, 2010],
            $orderedList
        );
    }

    public function testItCanDetermineIfItIsAfterAnother(): void
    {
        self::assertTrue(Year::of(2000)->isAfter(Year::of(1995)));
        self::assertFalse(Year::of(2000)->isAfter(Year::of(2000)));
        self::assertFalse(Year::of(2000)->isAfter(Year::of(2010)));
    }

    public function testItCanDetermineIfItIsBeforeAnother(): void
    {
        self::assertTrue(Year::of(2000)->isBefore(Year::of(2009)));
        self::assertFalse(Year::of(2000)->isBefore(Year::of(2000)));
        self::assertFalse(Year::of(2000)->isBefore(Year::of(1995)));
    }

    public function testAddingUnsupportedUnitThrowsException(): void
    {
        $this->expectException(UnsupportedTemporalType::class);

        Year::of(2000)->plus(1, ChronoUnit::Months());
    }

    public function testCanAddAmount(): void
    {
        $source = Year::of(2015);

        $amount = $this->createMock(TemporalAmount::class);
        $amount->expects(self::once())
               ->method('addTo')
               ->with($source)
               ->willReturn($source);

        $source->plusAmount($amount);
    }

    public function testCanSubtractAmount(): void
    {
        $source = Year::of(2015);

        $amount = $this->createMock(TemporalAmount::class);
        $amount->expects(self::once())
               ->method('subtractFrom')
               ->with($source)
               ->willReturn($source);

        $source->minusAmount($amount);
    }

    /**
     * @return array<string, array{Year, int, ChronoUnit, Year}>
     */
    public function provideForUnitMath(): array
    {
        $source = Year::of(2015);

        return [
            'positive-years' => [$source, 2, ChronoUnit::Years(), Year::of(2017)],
            'negative-years' => [$source, -2, ChronoUnit::Years(), Year::of(2013)],
            'zero-years' => [$source, 0, ChronoUnit::Years(), $source],
            'positive-decades' => [$source, 2, ChronoUnit::Decades(), Year::of(2035)],
            'negative-decades' => [$source, -2, ChronoUnit::Decades(), Year::of(1995)],
            'zero-decades' => [$source, 0, ChronoUnit::Decades(), $source],
            'positive-centuries' => [$source, 2, ChronoUnit::Centuries(), Year::of(2215)],
            'negative-centuries' => [$source, -2, ChronoUnit::Centuries(), Year::of(1815)],
            'zero-centuries' => [$source, 0, ChronoUnit::Centuries(), $source],
            'positive-millennia' => [$source, 2, ChronoUnit::Millennia(), Year::of(4015)],
            'negative-millennia' => [$source, -2, ChronoUnit::Millennia(), Year::of(15)],
            'zero-millennia' => [$source, 0, ChronoUnit::Millennia(), $source],
        ];
    }

    /**
     * @dataProvider provideForUnitMath
     *
     * @param Year       $expected
     * @param int        $amountToSubtract
     * @param ChronoUnit $unitToSubtract
     * @param Year       $source
     */
    public function testCanAddUnit(Year $source,
                                   int $amountToSubtract,
                                   ChronoUnit $unitToSubtract,
                                   Year $expected): void
    {
        self::assertHashEquals($expected, $source->plus($amountToSubtract, $unitToSubtract));
    }

    /**
     * @dataProvider provideForUnitMath
     *
     * @param Year       $expected
     * @param int        $amountToSubtract
     * @param ChronoUnit $unitToSubtract
     * @param Year       $source
     */
    public function testCanSubtractUnit(Year $expected,
                                        int $amountToSubtract,
                                        ChronoUnit $unitToSubtract,
                                        Year $source): void
    {
        self::assertHashEquals($expected, $source->minus($amountToSubtract, $unitToSubtract));
    }

    /**
     * @return array<string, array{Year, int, Year}>
     */
    public function provideForYearMath(): array
    {
        return [
            'positive' => [Year::of(2015), 2, Year::of(2017)],
            'negative' => [Year::of(2015), -2, Year::of(2013)],
        ];
    }

    /**
     * @dataProvider provideForYearMath
     *
     * @param Year $source
     * @param int  $amountToSubtract
     * @param Year $expected
     */
    public function testCanSubtractYears(Year $expected, int $amountToSubtract, Year $source): void
    {
        self::assertHashEquals($expected, $source->minusYears($amountToSubtract));
    }

    /**
     * @dataProvider provideForYearMath
     *
     * @param Year $source
     * @param int  $amountToAdd
     * @param Year $expected
     */
    public function testCanAddYears(Year $source, int $amountToAdd, Year $expected): void
    {
        self::assertHashEquals($expected, $source->plusYears($amountToAdd));
    }

    /**
     * @dataProvider provideSupportedFields
     *
     * @param ChronoField $field
     * @param bool        $expected
     */
    public function testCanRetrieveListOfSupportedUnits(ChronoField $field, bool $expected): void
    {
        $year = Year::of(2000);

        self::assertSame($expected, $year->supportsField($field));
    }

    public function testCanRetrieveValueOfUnit(): void
    {
        $expected = 2015;
        $year = Year::of($expected);

        self::assertSame($expected, $year->get(ChronoField::Year()));
    }

    public function testItCanBeTransformedToNativeDateTime(): void
    {
        $source = Year::of(2012);

        self::assertEquals(DateTimeImmutable::createFromFormat('Y', '2012'), $source->toNative());
    }

    /**
     * @return array<array-key, array{ChronoField, bool}>
     */
    public function provideSupportedFields(): array
    {
        return [
            [ChronoField::Year(), true],
            [ChronoField::DayOfMonth(), false],
        ];
    }

    /**
     * @return array<string, array{Year, ChronoField, int, Year}>
     */
    public function provideForFieldChange(): array
    {
        $source = Year::of(2015);

        return [
            'year-change' => [$source, ChronoField::Year(), 2017, Year::of(2017)],
            'year-same' => [$source, ChronoField::Year(), 2015, $source],
        ];
    }

    /**
     * @dataProvider provideForFieldChange
     *
     * @param Year          $source
     * @param TemporalField $field
     * @param int           $newValue
     * @param Year          $expected
     *
     * @return void
     */
    public function testItCanChangeField(Year $source,
                                         TemporalField $field,
                                         int $newValue,
                                         Year $expected): void
    {
        self::assertHashEquals($expected, $source->withField($field, $newValue));
    }

    public function testItWillThrowExceptionWhenChangingUnsupportedField(): void
    {
        $field = ChronoField::DayOfMonth();

        $this->expectExceptionObject(UnsupportedTemporalType::forField($field));

        Year::of(2015)->withField($field, 3);
    }

    public function testItCanBeUsedToAdjustDifferentTemporal(): void
    {
        $year = Year::of(2015);
        $temporal = YearMonth::of(2017, 3);

        self::assertHashEquals($year, $year->adjustInto($temporal)->year());
    }

    public function testItWillReturnSelfWhenUsedOnSameObject(): void
    {
        $year = Year::of(2015);
        $temporal = Year::of(2017);

        self::assertSame($year, $temporal->with($year));
    }

}