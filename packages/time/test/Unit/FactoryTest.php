<?php

declare(strict_types=1);

namespace ParTest\Time\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Par\Time\Exception\InvalidArgumentException;
use Par\Time\Factory;

class FactoryTest extends TimeTestCase
{

    /**
     * @return array<string, array{string, string, bool, bool}>
     */
    public function provideStringsInFormat(): array
    {
        return [
            'valid' => ['Y-m-d', '2000-1-1', true, false],
            'not in format' => ['Y-m-d', 'abc', false, false],
            'non existing date without wrapping' => ['Y-m-d', '2000-04-31', false, false],
            'non existing date with wrapping' => ['Y-m-d', '2000-04-31', true, true],
        ];
    }

    /**
     * @dataProvider provideStringsInFormat
     *
     * @param string $format
     * @param string $time
     * @param bool   $expected
     * @param bool   $allowWrapping
     */
    public function testCanValidateStringIsCorrectFormat(string $format,
                                                         string $time,
                                                         bool $expected,
                                                         bool $allowWrapping): void
    {
        self::assertEquals($expected, Factory::isValidForFormat($format, $time, $allowWrapping));
    }

    public function testCreateDate(): void
    {
        $dt = Factory::createDate(1975, 5, 21);

        self::assertSameDate($dt, 1975, 5, 21);
    }

    public function testCreateDateWithDay(): void
    {
        $dt = Factory::createDate(null, null, 21);

        self::assertSameDay($dt, 21);
    }

    public function testCreateDateWithDefaults(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $dt = Factory::createDate();

                $now = Factory::now();

                self::assertSame($now->format('c'), $dt->format('c'));
            }
        );
    }

    public function testCreateDateWithMonth(): void
    {
        $dt = Factory::createDate(null, 5);

        self::assertSameMonth($dt, 5);
    }

    public function testCreateDateWithTimezone(): void
    {
        $dt = Factory::createDate(1975, 5, 21, new DateTimeZone('America/New_York'));

        self::assertSameDate($dt, 1975, 5, 21);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateDateWithTimezoneString(): void
    {
        $dt = Factory::createDate(1975, 5, 21, 'America/New_York');

        self::assertSameDate($dt, 1975, 5, 21);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateDateWithYear(): void
    {
        $dt = Factory::createDate(1975);

        self::assertSameYear($dt, 1975);
    }

    public function testCreateDayWraps(): void
    {
        $d = Factory::create(2011, 1, 40, 0, 0, 0);

        self::assertDateTime($d, 2011, 2, 9, 0, 0, 0);
    }

    public function testCreateFromFormat(): void
    {
        $dt = Factory::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11');

        self::assertDateTime($dt, 1975, 5, 21, 22, 32, 11);
    }

    public function testCreateFromFormatWithTimezone(): void
    {
        $dt = Factory::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11', new DateTimeZone('America/New_York'));

        self::assertDateTime($dt, 1975, 5, 21, 22, 32, 11);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateFromFormatWithTimezoneString(): void
    {
        $dt = Factory::createFromFormat('Y-m-d H:i:s', '1975-05-21 22:32:11', 'America/New_York');

        self::assertDateTime($dt, 1975, 5, 21, 22, 32, 11);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateFromInstance(): void
    {
        $immutable = Factory::now();

        $dt = new DateTime();
        $fromDt = Factory::createFromInstance($dt);

        self::assertSame(Factory::createFromInstance($immutable), $immutable);
        self::assertSame($dt->format('c'), $fromDt->format('c'));
    }

    public function testCreateFromTimestamp(): void
    {
        $dt = Factory::createFromTimestamp(0);

        self::assertSameDate($dt, 1970, 1, 1);
    }

    public function testCreateFromTimestampWithTimezone(): void
    {
        // Toronto is -5 since no DST in Jan
        $dt = Factory::createFromTimestamp(0, new DateTimeZone('America/Toronto'));

        self::assertSameDate($dt, 1969, 12, 31);
        self::assertSame('America/Toronto', $dt->getTimezone()->getName());
    }

    public function testCreateFromTimestampWithTimezoneString(): void
    {
        $dt = Factory::createFromTimestamp(0, 'UTC');

        self::assertSameDate($dt, 1970, 1, 1);
        self::assertSame('UTC', $dt->getTimezone()->getName());
    }

    public function testCreateHandlesNegativeYear(): void
    {
        $dt = Factory::create(-1, 10, 12, 1, 2, 3);

        self::assertDateTime($dt, -1, 10, 12, 1, 2, 3);
    }

    public function testCreateHourWraps(): void
    {
        $d = Factory::create(2011, 1, 1, 24, 0, 0);

        self::assertDateTime($d, 2011, 1, 2, 0, 0, 0);
    }

    public function testCreateMinuteWraps(): void
    {
        $dt = Factory::create(2011, 1, 1, 0, 62, 0);

        self::assertDateTime($dt, 2011, 1, 1, 1, 2, 0);
    }

    public function testCreateMonthWraps(): void
    {
        $dt = Factory::create(2011, 0, 1, 0, 0, 0);

        self::assertDateTime($dt, 2010, 12, 1, 0, 0, 0);
    }

    public function testCreateSecondWraps(): void
    {
        $dt = Factory::create(2012, 1, 1, 0, 0, 61);
        self::assertDateTime($dt, 2012, 1, 1, 0, 1, 1);
    }

    public function testCreateTime(): void
    {
        $dt = Factory::createTime(23, 5, 21);

        self::assertSameTime($dt, 23, 5, 21);
    }

    public function testCreateTimeWithDefaults(): void
    {
        $dt = Factory::createTime();

        self::assertSame(Factory::now()->format('c'), $dt->format('c'));
    }

    public function testCreateTimeWithHour(): void
    {
        $dt = Factory::createTime(23);

        self::assertSameTime($dt, 23, 0, 0);
    }

    public function testCreateTimeWithMinute(): void
    {
        $dt = Factory::createTime(null, 5);

        self::assertSameTime($dt, date('H'), 5, date('s'));
    }

    public function testCreateTimeWithSecond(): void
    {
        $dt = Factory::createTime(null, null, 21);

        self::assertSameTime($dt, date('H'), date('i'), 21);
    }

    public function testCreateTimeWithTimezone(): void
    {
        $dt = Factory::createTime(23, 5, 21, new DateTimeZone('America/New_York'));

        self::assertSameTime($dt, 23, 5, 21);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateTimeWithTimezoneString(): void
    {
        $dt = Factory::createTime(23, 5, 21, 'America/New_York');

        self::assertSameTime($dt, 23, 5, 21);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateWithDateTimezone(): void
    {
        $dt = Factory::create(2012, 1, 1, 0, 0, 0, new DateTimeZone('America/New_York'));

        self::assertDateTime($dt, 2012, 1, 1, 0, 0, 0);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateWithDateTimezoneString(): void
    {
        $dt = Factory::create(2012, 1, 1, 0, 0, 0, 'America/New_York');

        self::assertDateTime($dt, 2012, 1, 1, 0, 0, 0);
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testCreateWithDay(): void
    {
        $dt = Factory::create(null, null, 21);

        self::assertSameDate($dt, date('Y'), date('n'), 21);
    }

    public function testCreateWithDefaults(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $dt = Factory::create();

                self::assertSame(Factory::now()->getTimestamp(), $dt->getTimestamp());
            }
        );
    }

    public function testCreateWithHourAndDefaultMinSecToZero(): void
    {
        $dt = Factory::create(null, null, null, 14);

        self::assertDateTime($dt, date('Y'), date('m'), date('d'), 14, 0, 0);
    }

    public function testCreateWithInvalidDay(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Factory::create(null, null, -3);
    }

    public function testCreateWithInvalidHour(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Factory::create(null, null, null, -6);
    }

    public function testCreateWithInvalidMinute(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Factory::create(2011, 1, 1, 0, -2, 0);
    }

    public function testCreateWithInvalidMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Factory::create(null, -5);
    }

    public function testCreateWithInvalidSecond(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Factory::create(2011, 1, 1, 0, 0, -2);
    }

    public function testCreateWithMinute(): void
    {
        $dt = Factory::create(null, null, null, null, 58);

        self::assertDateTime($dt, date('Y'), date('m'), date('d'), date('H'), 58, date('s'));
    }

    public function testCreateWithMonth(): void
    {
        $dt = Factory::create(null, 3);

        self::assertSameDate($dt, date('Y'), 3, date('d'));
    }

    public function testCreateWithSecond(): void
    {
        $dt = Factory::create(null, null, null, null, null, 59);

        self::assertDateTime($dt, date('Y'), date('m'), date('d'), date('H'), date('i'), 59);
    }

    public function testCreateWithYear(): void
    {
        $dt = Factory::create(2012);

        self::assertSameYear($dt, 2012);
    }

    public function testNow(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $dt = Factory::now();

                self::assertSame(time(), $dt->getTimestamp());
            }
        );
    }

    public function testNowWithTimezone(): void
    {
        $this->wrapWithTestNow(
            static function () {
                $dt = Factory::now('America/New_York');

                self::assertSame(time(), $dt->getTimestamp());
                self::assertSame('America/New_York', $dt->getTimezone()->getName());
            }
        );
    }

    public function testParseDefaultsToNow(): void
    {
        $dt = Factory::parse();

        self::assertDateTime($dt, date('Y'), date('m'), date('d'), date('H'), date('i'), date('s'));
    }

    /**
     * @return void
     */
    public function testParseRelativeWithTestValueSet(): void
    {
        $notNow = Factory::parse('2013-09-01 05:15:05');
        Factory::setTestNow($notNow);

        self::assertSame('2013-09-01 05:10:05', Factory::parse('5 minutes ago')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-25 05:15:05', Factory::parse('1 week ago')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-02 00:00:00', Factory::parse('tomorrow')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-01 00:00:00', Factory::parse('today')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-01 00:00:00', Factory::parse('midnight')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-31 00:00:00', Factory::parse('yesterday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-02 05:15:05', Factory::parse('+1 day')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-31 05:15:05', Factory::parse('-1 day')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-02 00:00:00', Factory::parse('next monday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-03 00:00:00', Factory::parse('next tuesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-04 00:00:00', Factory::parse('next wednesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-05 00:00:00', Factory::parse('next thursday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-06 00:00:00', Factory::parse('next friday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-07 00:00:00', Factory::parse('next saturday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-08 00:00:00', Factory::parse('next sunday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-26 00:00:00', Factory::parse('last monday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-27 00:00:00', Factory::parse('last tuesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-28 00:00:00', Factory::parse('last wednesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-29 00:00:00', Factory::parse('last thursday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-30 00:00:00', Factory::parse('last friday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-31 00:00:00', Factory::parse('last saturday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-08-25 00:00:00', Factory::parse('last sunday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-02 00:00:00', Factory::parse('this monday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-03 00:00:00', Factory::parse('this tuesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-04 00:00:00', Factory::parse('this wednesday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-05 00:00:00', Factory::parse('this thursday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-06 00:00:00', Factory::parse('this friday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-07 00:00:00', Factory::parse('this saturday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-01 00:00:00', Factory::parse('this sunday')->format('Y-m-d H:i:s'));
        self::assertSame('2013-10-01 05:15:05', Factory::parse('first day of next month')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-30 05:15:05', Factory::parse('last day of this month')->format('Y-m-d H:i:s'));
        self::assertSame('2013-09-30 05:15:05', Factory::parse('2013-09-30 05:15:05')->format('Y-m-d H:i:s'));
    }

    public function testParseThrowsInvalidArgumentExceptionWhenTimeIsNotAValidDateTime(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Factory::parse('abc');
    }

    public function testParseWithTestingAidSet(): void
    {
        $testDt = new DateTimeImmutable('2018-06-21 10:11:12');
        Factory::setTestNow($testDt);

        self::assertEquals($testDt, Factory::parse());
        self::assertEquals($testDt, Factory::parse(null));
        self::assertEquals($testDt, Factory::parse(''));
        self::assertEquals($testDt, Factory::parse(Factory::NOW));
    }

    public function testParseWithTestingAidSetWithCustomTimezone(): void
    {
        $testDt = (new DateTimeImmutable('2018-06-21 10:11:12'))->setTimezone(new DateTimeZone('America/New_York'));
        Factory::setTestNow($testDt);

        $actual = Factory::parse();
        self::assertSame($testDt->getTimezone()->getName(), $actual->getTimezone()->getName());

        $customTz = Factory::parse(Factory::NOW, 'Europe/London');
        self::assertSame('Europe/London', $customTz->getTimezone()->getName());
    }

    public function testTestingAidWithTestNowNotSet(): void
    {
        Factory::setTestNow();

        self::assertNull(Factory::getTestNow());
    }

    public function testTestingAidWithTestNowSet(): void
    {
        $testDt = new DateTimeImmutable('2018-06-21 10:11:12');
        Factory::setTestNow($testDt);

        self::assertSame($testDt, Factory::getTestNow());
    }

    public function testToday(): void
    {
        $dt = Factory::today();

        self::assertSame(date('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
    }

    public function testTodayWithTimezone(): void
    {
        $dt = Factory::today('America/New_York');
        $dt2 = new DateTimeImmutable('today', new DateTimeZone('America/New_York'));

        self::assertSame($dt2->format('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testTomorrow(): void
    {
        $dt = Factory::tomorrow();
        $dt2 = new DateTimeImmutable('tomorrow');

        self::assertSame($dt2->format('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
    }

    public function testTomorrowWithTimezone(): void
    {
        $dt = Factory::tomorrow('America/New_York');
        $dt2 = new DateTimeImmutable('tomorrow', new DateTimeZone('America/New_York'));

        self::assertSame($dt2->format('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }

    public function testWithFancyString(): void
    {
        $dt = Factory::parse('first day of January 2008');

        self::assertDateTime($dt, 2008, 1, 1, 0, 0, 0);
    }

    public function testYesterday(): void
    {
        $dt = Factory::yesterday();
        $dt2 = new DateTimeImmutable('yesterday');

        self::assertSame($dt2->format('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
    }

    public function testYesterdayWithTimezone(): void
    {
        $dt = Factory::yesterday('America/New_York');
        $dt2 = new DateTimeImmutable('yesterday', new DateTimeZone('America/New_York'));

        self::assertSame($dt2->format('Y-m-d 00:00:00'), $dt->format('Y-m-d H:i:s'));
        self::assertSame('America/New_York', $dt->getTimezone()->getName());
    }
}