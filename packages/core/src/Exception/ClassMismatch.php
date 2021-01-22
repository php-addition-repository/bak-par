<?php

declare(strict_types=1);

namespace Par\Core\Exception;

use Par\Core\Values;

final class ClassMismatch extends \RuntimeException implements ExceptionInterface
{

    public static function forInvalidArgument(string $function,
                                              int $argumentPos,
                                              string $argumentName,
                                              string $expectedType,
                                              string $actualType): self
    {
        return new self(
            sprintf(
                '%s(): Argument #%d ($%s) must be of type %s, %s given.',
                $function,
                $argumentPos,
                $argumentName,
                $expectedType,
                $actualType
            )
        );
    }

    public static function forExpectedInstance(object $expected, mixed $actual): self
    {
        return self::forExpectedType(get_class($expected), $actual);
    }

    private static function forExpectedType(string $expectedType, mixed $actual): self
    {
        return new self(
            sprintf(
                'Expected an instance of %s, got %s',
                $expectedType,
                Values::typeOf($actual)
            )
        );
    }
}