<?php

declare(strict_types=1);

namespace Par\Time;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception as GlobalException;
use Par\Time\Exception\InvalidArgumentException;

/**
 * A factory for handling native date-time operations.
 */
final class Factory
{
    public const NOW = 'now';
    public const TODAY = 'midnight';
    public const MIDNIGHT = 'midnight';
    public const YESTERDAY = 'yesterday, midnight';
    public const TOMORROW = 'tomorrow, midnight';

    private const RELATIVE_PATTERN = '/this|next|previous|tomorrow|yesterday|midnight|today|[+-]|first|last|ago/i';

    private static ?DateTimeImmutable $testNow = null;

    /**
     * Create a DateTimeImmutable instance from just a date. The time portion is set to now.
     *
     * @param int|null                 $year  The year to create an instance with.
     * @param int|null                 $month The month to create an instance with.
     * @param int|null                 $day   The day to create an instance with.
     * @param DateTimeZone|string|null $tz    The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function createDate(?int $year = null,
                                      ?int $month = null,
                                      ?int $day = null,
                                      DateTimeZone|string|null $tz = null): DateTimeImmutable
    {
        return static::create($year, $month, $day, null, null, null, $tz);
    }

    /**
     * Create a DateTimeImmutable from a specific date and time.
     *
     * If any of $year, $month or $day are set to null their now() values will be used.
     *
     * If $hour is null it will be set to its now() vale and the default values for $minute and $second will be their
     * now() values. If $hour is not null then the default values for $minute and $second will be 0 (zero).
     *
     * @param int|null                 $year   The year to create an instance with.
     * @param int|null                 $month  The month to create an instance with.
     * @param int|null                 $day    The day to create an instance with.
     * @param int|null                 $hour   The hour to create an instance with.
     * @param int|null                 $minute The minute to create an instance with.
     * @param int|null                 $second The second to create an instance with.
     * @param DateTimeZone|string|null $tz     The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function create(
        ?int $year = null,
        ?int $month = null,
        ?int $day = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null,
        DateTimeZone|string|null $tz = null
    ): DateTimeImmutable {
        $test = self::getTestNow();
        /** @psalm-suppress ImpureFunctionCall */
        $currentTime = $test instanceof DateTimeImmutable ? $test->getTimestamp() : time();

        $year = $year ?? (int)date('Y', $currentTime);
        $month = $month ?? (int)date('n', $currentTime);
        $day = $day ?? (int)date('j', $currentTime);

        if ($hour === null) {
            $hour = (int)date('G', $currentTime);
            $minute = $minute ?? (int)date('i', $currentTime);
            $second = $second ?? (int)date('s', $currentTime);
        } else {
            $minute = $minute ?? 0;
            $second = $second ?? 0;
        }

        $instance = static::createFromFormat(
            'Y-n-j G:i:s',
            sprintf('%s-%s-%s %s:%02s:%02s', 0, $month, $day, $hour, $minute, $second),
            $tz
        );

        return $instance->modify($year . ' year');
    }

    /**
     * Get the internal value for "now".
     *
     * @return DateTimeImmutable|null
     * @psalm-pure
     */
    public static function getTestNow(): ?DateTimeImmutable
    {
        /** @psalm-suppress ImpureStaticProperty */
        return self::$testNow;
    }

    /**
     * Set the internal value for "now".
     *
     * @param DateTimeImmutable|null $dateTime
     *
     * @return void
     */
    public static function setTestNow(?DateTimeImmutable $dateTime = null): void
    {
        self::$testNow = $dateTime;
    }

    /**
     * Create a DateTimeImmutable instance from a specific format.
     *
     * @param string                   $format The date() compatible format string.
     * @param string                   $time   The formatted date string to interpret.
     * @param DateTimeZone|string|null $tz     The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @throws InvalidArgumentException
     * @psalm-pure
     */
    public static function createFromFormat(string $format,
                                            string $time,
                                            DateTimeZone|string|null $tz = null): DateTimeImmutable
    {
        if ($tz !== null) {
            $dt = DateTimeImmutable::createFromFormat($format, $time, static::safeCreateDateTimeZone($tz));
        } else {
            $dt = DateTimeImmutable::createFromFormat($format, $time);
        }

        if ($dt == false) {
            $errors = DateTimeImmutable::getLastErrors();
            throw new InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
        }

        return $dt;
    }

    /**
     * Creates a DateTimeZone from a string or a DateTimeZone
     *
     * @param DateTimeZone|string|null $object The value to convert.
     *
     * @return DateTimeZone
     * @psalm-pure
     */
    private static function safeCreateDateTimeZone(DateTimeZone|string|null $object): DateTimeZone
    {
        if ($object === null) {
            /** @psalm-suppress ImpureFunctionCall */
            return new DateTimeZone(date_default_timezone_get());
        }

        if ($object instanceof DateTimeZone) {
            return $object;
        }

        return new DateTimeZone($object);
    }

    /**
     * Create a DateTimeImmutable from a DateTimeInterface
     *
     * @param DateTimeInterface $dateTime The datetime instance to convert.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function createFromInstance(DateTimeInterface $dateTime): DateTimeImmutable
    {
        if ($dateTime instanceof DateTimeImmutable) {
            return $dateTime;
        }

        /** @psalm-suppress ImpureMethodCall */
        return self::createFromFormat(DATE_ATOM, $dateTime->format(DATE_ATOM), $dateTime->getTimezone());
    }

    /**
     * Create a DateTimeImmutable instance from a timestamp
     *
     * @param int                      $timestamp The timestamp to create an instance from.
     * @param DateTimeZone|string|null $tz        The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function createFromTimestamp(int $timestamp, $tz = null): DateTimeImmutable
    {
        return static::now($tz)->setTimestamp($timestamp);
    }

    /**
     * @param DateTimeZone|string|null $tz The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function now(DateTimeZone|string|null $tz = null): DateTimeImmutable
    {
        return static::parse(self::NOW, $tz);
    }

    /**
     * @param string|null              $time
     * @param DateTimeZone|string|null $tz The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @throws InvalidArgumentException
     * @psalm-pure
     */
    public static function parse(?string $time = self::NOW, $tz = null): DateTimeImmutable
    {
        if ($tz !== null) {
            $tz = self::safeCreateDateTimeZone($tz);
        }

        $time = $time ?? self::NOW;

        try {
            $testNow = static::getTestNow();
            if ($testNow === null) {
                return new DateTimeImmutable($time, $tz);
            }

            $relative = static::hasRelativeKeywords($time);
            if (!empty($time) && $time !== self::NOW && !$relative) {
                return new DateTimeImmutable($time, $tz);
            }

            $testNow = clone $testNow;
            if ($relative) {
                $testNow = $testNow->modify($time);
            }

            if ($tz !== null && $tz !== $testNow->getTimezone()) {
                $testNow = $testNow->setTimezone(self::safeCreateDateTimeZone($tz));
            }

            return $testNow;
        } catch (GlobalException $e) {
            // Transform it into an exception we are more likely to ignore in our IDE
            throw new InvalidArgumentException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Determine if there is a relative keyword in the time string, this is to
     * create dates relative to now for tests instances. e.g.: next tuesday
     *
     * @param string $time The time string to check.
     *
     * @return bool true if there is a keyword, otherwise false
     * @psalm-pure
     */
    private static function hasRelativeKeywords(string $time): bool
    {
        if (self::isTimeExpression($time)) {
            return true;
        }

        // skip common format with a '-' in it
        if (preg_match('/\d{4}-\d{1,2}-\d{1,2}/', $time) !== 1) {
            return preg_match(static::RELATIVE_PATTERN, $time) > 0;
        }

        return false;
    }

    /**
     * Determine if there is just a time in the time string
     *
     * @param string $time The time string to check.
     *
     * @return bool true if there is a keyword, otherwise false
     * @psalm-pure
     */
    private static function isTimeExpression(string $time): bool
    {
        // Just a time
        if (preg_match('/^[0-2]?[\d]:[0-5][\d](?::[0-5][\d])?$/', $time)) {
            return true;
        }

        return false;
    }

    /**
     * Create a DateTimeImmutable instance from just a time. The date portion is set to today.
     *
     * @param int|null                 $hour   The hour to create an instance with.
     * @param int|null                 $minute The minute to create an instance with.
     * @param int|null                 $second The second to create an instance with.
     * @param DateTimeZone|string|null $tz     The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function createTime(?int $hour = null,
                                      ?int $minute = null,
                                      ?int $second = null,
                                      $tz = null): DateTimeImmutable
    {
        return static::create(null, null, null, $hour, $minute, $second, $tz);
    }

    /**
     * Determines if provided date time parts construct into a valid date
     *
     * @param int                      $year          The year to create an instance with.
     * @param int                      $month         The month to create an instance with.
     * @param int                      $day           The day to create an instance with.
     * @param DateTimeZone|string|null $tz            The timezone for the instance. Defaults to default timezone.
     * @param bool                     $allowWrapping Allow wrapping (April 31st to May 1st, )
     *
     * @return bool
     * @psalm-pure
     */
    public static function isValidDate(int $year, int $month, int $day, $tz = null, bool $allowWrapping = false): bool
    {
        DateTimeImmutable::createFromFormat(
            'Y-n-j',
            sprintf('%d-%d-%d', $year, $month, $day),
            self::safeCreateDateTimeZone($tz)
        );
        $lastDateTimeErrors = DateTimeImmutable::getLastErrors();

        return $lastDateTimeErrors['error_count'] === 0 && ($allowWrapping === true || $lastDateTimeErrors['warning_count'] === 0);
    }

    /**
     * Determines if provided time is according to format.
     *
     * @param string $format        The date() compatible format string.
     * @param string $time          The formatted date string to interpret.
     * @param bool   $allowWrapping Allow wrapping (April 31st to May 1st, )
     *
     * @return bool
     * @psalm-pure
     */
    public static function isValidForFormat(string $format, string $time, bool $allowWrapping = false): bool
    {
        DateTimeImmutable::createFromFormat($format, $time);
        $lastDateTimeErrors = DateTimeImmutable::getLastErrors();

        return $lastDateTimeErrors['error_count'] === 0 && ($allowWrapping === true || $lastDateTimeErrors['warning_count'] === 0);
    }

    /**
     * @param DateTimeZone|string|null $tz The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function today($tz = null): DateTimeImmutable
    {
        return static::parse(self::TODAY, $tz);
    }

    /**
     * @param DateTimeZone|string|null $tz The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function tomorrow($tz = null): DateTimeImmutable
    {
        return static::parse(self::TOMORROW, $tz);
    }

    /**
     * @param DateTimeZone|string|null $tz The timezone for the instance. Defaults to default timezone.
     *
     * @return DateTimeImmutable
     * @psalm-pure
     */
    public static function yesterday($tz = null): DateTimeImmutable
    {
        return static::parse(self::YESTERDAY, $tz);
    }
}