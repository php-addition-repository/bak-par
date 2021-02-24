<?php

declare(strict_types=1);

namespace Par\Core\Exception;

use RuntimeException;

final class ClassMismatch extends RuntimeException implements ExceptionInterface
{

    public static function forInvalidArgument(string $function,
                                              int $argumentPos,
                                              string $argumentName,
                                              string $expectedType,
                                              string $actualType): self
    {
        return new self(
            sprintf(
                '%s(): Argument #%d ($%s) must be of type %s, got %s.',
                $function,
                $argumentPos,
                $argumentName,
                $expectedType,
                $actualType
            )
        );
    }

    public static function forUnexpectedInstance(object $expectedInstance, mixed $actualValue): self
    {
        return new self(
            sprintf(
                'Expected an instance of %s, got %s',
                get_class($expectedInstance),
                get_debug_type($actualValue)
            )
        );
    }
}