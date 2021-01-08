<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit;

use Par\Core\Hashable;
use Par\Core\PHPUnit\Constraint\HashableEquals;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\LogicalNot;

/**
 * Include this trait in your unit tests to easily compare a value against an expected instance of a
 * `\Par\Core\Hashable` implementation.
 */
trait HashableAssertions
{
    /**
     * @param Hashable $expected The expected hashable
     * @param mixed    $actual   The actual value to test
     * @param string   $message  Message to return when expected and actual are equal
     *
     * @return void
     */
    public static function assertHashNotEquals(Hashable $expected, mixed $actual, string $message = ''): void
    {
        Assert::assertThat(
            $actual,
            new LogicalNot(
                new HashableEquals($expected)
            ),
            $message
        );
    }

    /**
     * @param Hashable $expected The expected hashable
     * @param mixed    $actual   The actual value to test
     * @param string   $message  Message to return when expected and actual are not equal
     *
     * @return void
     */
    public static function assertHashEquals(Hashable $expected, mixed $actual, string $message = ''): void
    {
        Assert::assertThat(
            $actual,
            new HashableEquals($expected),
            $message
        );
    }
}
