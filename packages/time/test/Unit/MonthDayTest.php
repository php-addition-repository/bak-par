<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Factory;
use Par\Time\Month;
use Par\Time\MonthDay;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use PHPUnit\Framework\TestCase;

class MonthDayTest extends TestCase
{
    use TimeTestCaseTrait;
    use HashableAssertions;

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

    /**
     * @test
     */
    public function itCanBeCreatedFromValue(): void
    {
        $expectedMonth = 7;
        $expectedDayOfMonth = 4;
        $monthDay = MonthDay::of($expectedMonth, $expectedDayOfMonth);

        self::assertSame($expectedMonth, $monthDay->monthValue());
        self::assertSame($expectedDayOfMonth, $monthDay->dayOfMonth());
    }

    /**
     * @test
     */
    public function itCanBeCreatedForCurrentYear(): void
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
     *
     * @test
     */
    public function itCanBeCreatedFromString(string $text, MonthDay $expected): void
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
     * @test
     * @dataProvider provideNative
     *
     * @param MonthDay          $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function itCanBeCreatedFromNativeDateTime(MonthDay $expected, DateTimeInterface $native): void
    {
        self::assertHashEquals($expected, MonthDay::fromNative($native));
    }

    /**
     * @test
     */
    public function itCanDetermineEqualityWithOther(): void
    {
        $monthDay = MonthDay::of(11, 4);
        $otherMonthDay = MonthDay::of(5, 12);

        self::assertTrue($monthDay->equals($monthDay));
        self::assertFalse($monthDay->equals($otherMonthDay));
        self::assertFalse($monthDay->equals(null));
    }

    /**
     * @test
     */
    public function itCanBeTransformedToString(): void
    {
        self::assertSame(MonthDay::of(5, 1)->toString(), '--05-01');
    }

    /**
     * @test
     */
    public function ItWillThrowExceptionWhenCreatingFromInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MonthDay::parse('The year 2000');
    }

    /**
     * @test
     */
    public function itCanUpdateMonth(): void
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

    /**
     * @test
     */
    public function itCanUpdateDayOfMonth(): void
    {
        $monthDay = MonthDay::of(3, 12);

        self::assertSame(5, $monthDay->withDayOfMonth(5)->dayOfMonth());
        self::assertHashEquals(Month::of(3), $monthDay->withDayOfMonth(5)->month());
        self::assertHashNotEquals($monthDay, $monthDay->withDayOfMonth(5));
    }

    /**
     * @test
     */
    public function itIsHashable(): void
    {
        self::assertSame(1203, MonthDay::of(12, 3)->hash());
    }
}