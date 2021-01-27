<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoField;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Factory;
use Par\Time\Month;
use Par\Time\MonthDay;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use Par\Time\Temporal\Temporal;
use PHPUnit\Framework\TestCase;

class MonthDayTest extends TestCase
{
    use TimeTestCaseTrait;
    use HashableAssertions;

    public function testItCanDetermineIfFieldIsSupported(): void
    {
        $source = MonthDay::of(Month::January(), 1);
        self::assertTrue($source->supportsField(ChronoField::MonthOfYear()));
        self::assertTrue($source->supportsField(ChronoField::DayOfMonth()));
        self::assertFalse($source->supportsField(ChronoField::Year()));
    }

    public function testItCanGetValueOfField(): void
    {
        $source = MonthDay::of(Month::January(), 1);

        self::assertSame($source->monthValue(), $source->get(ChronoField::MonthOfYear()));
        self::assertSame($source->dayOfMonth(), $source->get(ChronoField::DayOfMonth()));
    }

    public function testItWillThrowExceptionWhenGettingUnsupportedField(): void
    {
        $unsupportedField = ChronoField::Year();

        $this->expectExceptionObject(UnsupportedTemporalType::forField($unsupportedField));

        $source = MonthDay::of(Month::January(), 1);

        $source->get($unsupportedField);
    }

    /**
     * @return array<string, array{string, MonthDay}>
     */
    public function provideStrings(): array
    {
        return [
            'month < 10' => ['--03-12', MonthDay::of(3, 12)],
            'month > 10' => ['--11-12', MonthDay::of(11, 12)],
            'day < 10' => ['--11-02', MonthDay::of(11, 2)],
        ];
    }

    public function testItCanBeCreatedFromValue(): void
    {
        $expectedMonth = 7;
        $expectedDayOfMonth = 4;
        $monthDay = MonthDay::of($expectedMonth, $expectedDayOfMonth);

        self::assertSame($expectedMonth, $monthDay->monthValue());
        self::assertSame($expectedDayOfMonth, $monthDay->dayOfMonth());
    }

    public function testItCanBeCreatedForCurrentYear(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $now = Factory::now();

                $currentMonthDay = MonthDay::now();

                self::assertSameMonth($now, $currentMonthDay->monthValue());
                self::assertSameDay($now, $currentMonthDay->dayOfMonth());
            }
        );
    }

    /**
     * @dataProvider provideStrings
     *
     * @param string   $text
     * @param MonthDay $expected
     */
    public function testItCanBeCreatedFromString(string $text, MonthDay $expected): void
    {
        self::assertHashEquals($expected, MonthDay::parse($text));
    }

    /**
     * @return array<string, array{MonthDay, DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                MonthDay::of(1, 14),
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                MonthDay::of(7, 20),
                Factory::createDate(2019, 7, 20),
            ],
        ];
    }

    /**
     * @dataProvider provideNative
     *
     * @param MonthDay          $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function testItCanBeCreatedFromNativeDateTime(MonthDay $expected, DateTimeInterface $native): void
    {
        self::assertHashEquals($expected, MonthDay::fromNative($native));
    }

    public function testItCanDetermineEqualityWithOther(): void
    {
        $monthDay = MonthDay::of(11, 4);
        $otherMonthDay = MonthDay::of(5, 12);

        self::assertTrue($monthDay->equals(clone $monthDay));
        self::assertFalse($monthDay->equals($otherMonthDay));
        self::assertFalse($monthDay->equals(null));
    }

    public function testItCanBeTransformedToString(): void
    {
        self::assertSame(MonthDay::of(5, 1)->toString(), '--05-01');
    }

    public function testItWillThrowExceptionWhenCreatingFromInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MonthDay::parse('The year 2000');
    }

    public function testItCanUpdateMonth(): void
    {
        $monthDay = MonthDay::of(3, 12);

        self::assertSame($monthDay->dayOfMonth(), $monthDay->withMonth(Month::of(4))->dayOfMonth());
        self::assertHashEquals(
            Month::of(4),
            $monthDay->withMonth(Month::of(4))->month()
        );
        self::assertHashNotEquals($monthDay, $monthDay->withMonth(Month::of(4)));

        self::assertSame($monthDay->dayOfMonth(), $monthDay->withMonth(4)->dayOfMonth());
        self::assertHashEquals(Month::of(4), $monthDay->withMonth(4)->month());
        self::assertHashNotEquals($monthDay, $monthDay->withMonth(4));
    }

    public function testItCanUpdateDayOfMonth(): void
    {
        $monthDay = MonthDay::of(3, 12);

        self::assertSame(5, $monthDay->withDayOfMonth(5)->dayOfMonth());
        self::assertHashEquals(Month::of(3), $monthDay->withDayOfMonth(5)->month());
        self::assertHashNotEquals($monthDay, $monthDay->withDayOfMonth(5));
    }

    public function testItIsHashable(): void
    {
        self::assertSame(1203, MonthDay::of(12, 3)->hash());
    }

    public function testItCanBeNaturallySorted(): void
    {
        $list = [
            MonthDay::of(3, 3),
            MonthDay::of(5, 1),
            MonthDay::of(3, 2),
        ];

        uasort(
            $list,
            static function (MonthDay $a, MonthDay $b): int {
                return $a->compareTo($b);
            }
        );

        $orderedList = [];
        foreach ($list as $item) {
            $orderedList[] = $item->toString();
        }

        self::assertSame(
            ['--03-02', '--03-03', '--05-01'],
            $orderedList
        );
    }

    public function testItCanDetermineIfItIsAfterAnother(): void
    {
        $current = MonthDay::of(6, 6);
        $monthAfter = MonthDay::of(7, 6);
        $monthBefore = MonthDay::of(5, 6);
        $dayAfter = MonthDay::of(6, 7);
        $dayBefore = MonthDay::of(6, 5);

        self::assertTrue($current->isAfter($monthBefore));
        self::assertTrue($current->isAfter($dayBefore));
        self::assertFalse($current->isAfter($current));
        self::assertFalse($current->isAfter($monthAfter));
        self::assertFalse($current->isAfter($dayAfter));
    }

    public function testItCanDetermineIfItIsBeforeAnother(): void
    {
        $current = MonthDay::of(6, 6);
        $monthAfter = MonthDay::of(7, 6);
        $monthBefore = MonthDay::of(5, 6);
        $dayAfter = MonthDay::of(6, 7);
        $dayBefore = MonthDay::of(6, 5);

        self::assertTrue($current->isBefore($monthAfter));
        self::assertTrue($current->isBefore($dayAfter));
        self::assertFalse($current->isBefore($current));
        self::assertFalse($current->isBefore($monthBefore));
        self::assertFalse($current->isBefore($dayBefore));
    }

    public function testItCanBeUsedToAdjustDifferentTemporal(): void
    {
        $source = MonthDay::of(3, 12);

        $temporal = $this->createMock(Temporal::class);
        $temporal->expects(self::exactly(2))->method('withField')
                 ->withConsecutive(
                     [ChronoField::MonthOfYear(), $source->monthValue()],
                     [ChronoField::DayOfMonth(), $source->dayOfMonth()]
                 )->willReturnSelf();

        self::assertSame($temporal, $source->adjustInto($temporal));
    }
}