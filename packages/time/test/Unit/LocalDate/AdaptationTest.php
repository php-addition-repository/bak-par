<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\LocalDate;

use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoField;
use Par\Time\Chrono\ChronoUnit;
use Par\Time\LocalDate;
use Par\Time\Temporal\TemporalAmount;
use Par\Time\Temporal\TemporalField;
use PHPUnit\Framework\TestCase;

final class AdaptationTest extends TestCase
{
    use HashableAssertions;

    public function testCanAddAmount(): void
    {
        $source = LocalDate::of(2015, 2, 3);

        $amount = $this->createMock(TemporalAmount::class);
        $amount->expects(self::once())
               ->method('addTo')
               ->with($source)
               ->willReturn($source);

        $source->plusAmount($amount);
    }

    public function testCanSubtractAmount(): void
    {
        $source = LocalDate::of(2015, 2, 3);

        $amount = $this->createMock(TemporalAmount::class);
        $amount->expects(self::once())
               ->method('subtractFrom')
               ->with($source)
               ->willReturn($source);

        $source->minusAmount($amount);
    }

    /**
     * @return array<string, array{LocalDate, int, ChronoUnit, LocalDate}>
     */
    public function provideForUnitMath(): array
    {
        $list = [];

        $unit = ChronoUnit::Years();
        foreach ($this->provideForYearMath() as $key => $arguments) {
            [$source, $amount, $expected] = $arguments;
            $list[$unit->toString() . ' ' . $key] = [$source, $amount, $unit, $expected];
        }

        $unit = ChronoUnit::Months();
        foreach ($this->provideForMonthMath() as $key => $arguments) {
            [$source, $amount, $expected] = $arguments;
            $list[$unit->toString() . ' ' . $key] = [$source, $amount, $unit, $expected];
        }

        $unit = ChronoUnit::Days();
        foreach ($this->provideForDayMath() as $key => $arguments) {
            [$source, $amount, $expected] = $arguments;
            $list[$unit->toString() . ' ' . $key] = [$source, $amount, $unit, $expected];
        }

        return $list;
    }

    /**
     * @dataProvider provideForUnitMath
     *
     * @param LocalDate  $expected
     * @param int        $amountToSubtract
     * @param ChronoUnit $unitToSubtract
     * @param LocalDate  $source
     */
    public function testCanAddUnit(LocalDate $source,
                                   int $amountToSubtract,
                                   ChronoUnit $unitToSubtract,
                                   LocalDate $expected): void
    {
        self::assertHashEquals($expected, $source->plus($amountToSubtract, $unitToSubtract));
    }

    /**
     * @dataProvider provideForUnitMath
     *
     * @param LocalDate  $expected
     * @param int        $amountToSubtract
     * @param ChronoUnit $unitToSubtract
     * @param LocalDate  $source
     */
    public function testCanSubtractUnit(LocalDate $expected,
                                        int $amountToSubtract,
                                        ChronoUnit $unitToSubtract,
                                        LocalDate $source): void
    {
        self::assertHashEquals($expected, $source->minus($amountToSubtract, $unitToSubtract));
    }

    /**
     * @return array<string, array{LocalDate, int, LocalDate}>
     */
    public function provideForYearMath(): array
    {
        $source = LocalDate::of(2015, 3, 2);

        return [
            'positive' => [$source, 2, LocalDate::of(2017, 3, 2)],
            'negative' => [$source, -2, LocalDate::of(2013, 3, 2)],
        ];
    }

    /**
     * @dataProvider provideForYearMath
     *
     * @param LocalDate $source
     * @param int       $amountToSubtract
     * @param LocalDate $expected
     */
    public function testCanSubtractYears(LocalDate $expected, int $amountToSubtract, LocalDate $source): void
    {
        self::assertHashEquals($expected, $source->minusYears($amountToSubtract));
    }

    /**
     * @dataProvider provideForYearMath
     *
     * @param LocalDate $source
     * @param int       $amountToAdd
     * @param LocalDate $expected
     */
    public function testCanAddYears(LocalDate $source, int $amountToAdd, LocalDate $expected): void
    {
        self::assertHashEquals($expected, $source->plusYears($amountToAdd));
    }

    /**
     * @return array<string, array{LocalDate, int, LocalDate}>
     */
    public function provideForMonthMath(): array
    {
        $source = LocalDate::of(2015, 3, 2);

        return [
            'positive' => [$source, 2, LocalDate::of(2015, 5, 2)],
            'positive-overflow' => [$source, 12, LocalDate::of(2016, 3, 2)],
            'negative' => [$source, -2, LocalDate::of(2015, 1, 2)],
            'negative-overflow' => [$source, -12, LocalDate::of(2014, 3, 2)],
        ];
    }

    /**
     * @dataProvider provideForMonthMath
     *
     * @param LocalDate $source
     * @param int       $amountToSubtract
     * @param LocalDate $expected
     */
    public function testCanSubtractMonths(LocalDate $expected, int $amountToSubtract, LocalDate $source): void
    {
        self::assertHashEquals($expected, $source->minusMonths($amountToSubtract));
    }

    /**
     * @dataProvider provideForMonthMath
     *
     * @param LocalDate $source
     * @param int       $amountToAdd
     * @param LocalDate $expected
     */
    public function testCanAddMonths(LocalDate $source, int $amountToAdd, LocalDate $expected): void
    {
        self::assertHashEquals($expected, $source->plusMonths($amountToAdd));
    }

    /**
     * @return array<string, array{LocalDate, int, LocalDate}>
     */
    public function provideForDayMath(): array
    {
        $source = LocalDate::of(2015, 3, 2);

        return [
            'positive' => [$source, 2, LocalDate::of(2015, 3, 4)],
            'positive-overflow' => [$source, 32, LocalDate::of(2015, 4, 3)],
            'negative' => [$source, -2, LocalDate::of(2015, 2, 28)],
            'negative-overflow' => [$source, -32, LocalDate::of(2015, 1, 29)],
        ];
    }

    /**
     * @dataProvider provideForDayMath
     *
     * @param LocalDate $source
     * @param int       $amountToSubtract
     * @param LocalDate $expected
     */
    public function testCanSubtractDays(LocalDate $expected, int $amountToSubtract, LocalDate $source): void
    {
        self::assertHashEquals($expected, $source->minusDays($amountToSubtract));
    }

    /**
     * @dataProvider provideForDayMath
     *
     * @param LocalDate $source
     * @param int       $amountToAdd
     * @param LocalDate $expected
     */
    public function testCanAddDays(LocalDate $source, int $amountToAdd, LocalDate $expected): void
    {
        self::assertHashEquals($expected, $source->plusDays($amountToAdd));
    }

    /**
     * @return array<array-key, array{ChronoField, bool}>
     */
    public function provideSupportedFields(): array
    {
        $fields = ChronoField::values();

        $list = [];
        foreach ($fields as $field) {
            $list[$field->toString()] = [$field, $field->isDateBased()];
        }
        return $list;
    }

    /**
     * @dataProvider provideSupportedFields
     *
     * @param ChronoField $field
     * @param bool        $expected
     */
    public function testCanRetrieveListOfSupportedUnits(ChronoField $field, bool $expected): void
    {
        $source = LocalDate::of(2015, 2, 3);

        self::assertSame($expected, $source->supportsField($field));
    }

    /**
     * @return array<string, array{LocalDate, ChronoField, int, LocalDate}>
     */
    public function provideForFieldChange(): array
    {
        $source = LocalDate::of(2015, 3, 2);

        return [
            'year-change' => [$source, ChronoField::Year(), 2017, LocalDate::of(2017, 3, 2)],
            'month-of-year-change' => [$source, ChronoField::MonthOfYear(), 2, LocalDate::of(2015, 2, 2)],
            'day-of-month-change' => [$source, ChronoField::DayOfMonth(), 4, LocalDate::of(2015, 3, 4)],
            'day-of-year-change' => [$source, ChronoField::DayOfYear(), 6, LocalDate::of(2015, 1, 6)],
            'day-of-week-change' => [$source, ChronoField::DayOfWeek(), 3, LocalDate::of(2015, 3, 4)],
            'year-same' => [$source, ChronoField::Year(), 2015, $source],
            'month-of-year-same' => [$source, ChronoField::MonthOfYear(), 3, $source],
            'day-of-month-same' => [$source, ChronoField::DayOfMonth(), 2, $source],
            'day-of-year-same' => [$source, ChronoField::DayOfYear(), 61, $source],
            'day-of-week-same' => [$source, ChronoField::DayOfWeek(), 1, $source],
        ];
    }

    /**
     * @dataProvider provideForFieldChange
     *
     * @param LocalDate     $source
     * @param TemporalField $field
     * @param int           $newValue
     * @param LocalDate     $expected
     *
     * @return void
     */
    public function testItCanChangeField(LocalDate $source,
                                         TemporalField $field,
                                         int $newValue,
                                         LocalDate $expected): void
    {
        self::assertHashEquals($expected, $source->withField($field, $newValue));
    }
}