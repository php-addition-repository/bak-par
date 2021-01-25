<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Par\Core\PHPUnit\EnumTestCaseTrait;
use Par\Core\PHPUnit\HashableAssertions;
use Par\Time\Chrono\ChronoField;
use Par\Time\DayOfWeek;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Exception\UnsupportedTemporalType;
use Par\Time\Factory;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use PHPUnit\Framework\TestCase;

class DayOfWeekTest extends TestCase
{
    use HashableAssertions;
    use EnumTestCaseTrait {
        EnumTestCaseTrait::setUp as enumSetup;
    }

    use TimeTestCaseTrait {
        TimeTestCaseTrait::setUp as timeSetup;
    }

    public function testItCanDetermineIfFieldIsSupported(): void
    {
        $source = DayOfWeek::Friday();
        self::assertTrue($source->supportsField(ChronoField::DayOfWeek()));
        self::assertFalse($source->supportsField(ChronoField::DayOfMonth()));
    }

    public function testItCanGetValueOfField(): void
    {
        $source = DayOfWeek::Friday();

        self::assertSame($source->value(), $source->get(ChronoField::DayOfWeek()));
    }

    public function testItWillThrowExceptionWhenGettingUnsupportedField(): void
    {
        $unsupportedField = ChronoField::DayOfMonth();

        $this->expectExceptionObject(UnsupportedTemporalType::forField($unsupportedField));

        $source = DayOfWeek::Monday();

        $source->get($unsupportedField);
    }

    public function testItWillReturnValue(): void
    {
        self::assertSame(2, DayOfWeek::Tuesday()->value());
    }

    public function testItCanBeCreatedFromValue(): void
    {
        $expected = DayOfWeek::Thursday();

        self::assertHashEquals($expected, DayOfWeek::of(4));
    }

    public function testItWillThrowInvalidArgumentExceptionWhenCreateFromOutOfRangeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DayOfWeek::of(8);
    }

    /**
     * @return array<string, array{DayOfWeek, DateTimeInterface}>
     */
    public function provideNative(): array
    {
        return [
            DateTime::class => [
                DayOfWeek::Thursday(),
                DateTime::createFromImmutable(Factory::createDate(2021, 1, 14)),
            ],
            DateTimeImmutable::class => [
                DayOfWeek::Wednesday(),
                Factory::createDate(2021, 1, 20),
            ],
        ];
    }

    /**
     * @dataProvider provideNative
     *
     * @param DayOfWeek         $expected
     * @param DateTimeInterface $native
     *
     * @return void
     */
    public function testItCanBeCreatedFromNativeDateTime(DayOfWeek $expected, DateTimeInterface $native): void
    {
        self::assertSame($expected, DayOfWeek::fromNative($native));
    }

    public function testItWillContainAllDaysOfWeek(): void
    {
        self::assertSame(
            [
                DayOfWeek::Monday(),
                DayOfWeek::Tuesday(),
                DayOfWeek::Wednesday(),
                DayOfWeek::Thursday(),
                DayOfWeek::Friday(),
                DayOfWeek::Saturday(),
                DayOfWeek::Sunday(),
            ],
            DayOfWeek::values()
        );
    }

    public function testItCanBeMovedViaSubtraction(): void
    {
        self::assertSame(DayOfWeek::Sunday(), DayOfWeek::Monday()->minus(1));
        self::assertSame(DayOfWeek::Saturday(), DayOfWeek::Monday()->minus(9));
        self::assertSame(DayOfWeek::Wednesday(), DayOfWeek::Monday()->minus(-2));
        self::assertSame(DayOfWeek::Monday(), DayOfWeek::Monday()->minus(0));
        self::assertSame(DayOfWeek::Monday(), DayOfWeek::Monday()->minus(14));
    }

    public function testItCanBeMovedViaAddition(): void
    {
        self::assertSame(DayOfWeek::Tuesday(), DayOfWeek::Monday()->plus(1));
        self::assertSame(DayOfWeek::Wednesday(), DayOfWeek::Monday()->plus(9));
        self::assertSame(DayOfWeek::Saturday(), DayOfWeek::Monday()->plus(-2));
        self::assertSame(DayOfWeek::Monday(), DayOfWeek::Monday()->plus(0));
        self::assertSame(DayOfWeek::Monday(), DayOfWeek::Monday()->plus(14));
    }

    public function testItCanReturnTheDayOfWeekForToday(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(DayOfWeek::Friday(), DayOfWeek::today());
            },
            Factory::createDate(2021, 1, 15)
        );
    }

    public function testItCanReturnTheDayOfWeekForTomorrow(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(DayOfWeek::Saturday(), DayOfWeek::tomorrow());
            },
            Factory::createDate(2021, 1, 15)
        );
    }

    public function testItCanReturnTheDayOfWeekForYesterday(): void
    {
        $this->wrapWithTestNow(
            static function () {
                self::assertSame(DayOfWeek::Thursday(), DayOfWeek::yesterday());
            },
            Factory::createDate(2021, 1, 15)
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->enumSetup();
        $this->timeSetup();
    }
}
