<?php

declare(strict_types=1);

namespace Par\Time;

use Par\Time\Exception\InvalidArgumentException;

/**
 * @internal
 * @noinspection PhpHierarchyChecksInspection
 */
final class Assert extends \Webmozart\Assert\Assert
{
    public static function date(int $year, int $month, int $dayOfMonth, string $message = ''): void
    {
        if (!Factory::isValidDate($year, $month, $dayOfMonth)) {
            static::reportInvalidArgument(
                \sprintf(
                    $message ?: 'Expected a valid date, got %d-%02d-%02d.',
                    $year,
                    $month,
                    $dayOfMonth
                )
            );
        }
    }

    /**
     * @param string $message
     *
     * @return void
     * @psalm-pure
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }

}