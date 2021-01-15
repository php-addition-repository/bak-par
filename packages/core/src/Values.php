<?php

declare(strict_types=1);

namespace Par\Core;

use Closure;
use Stringable;

/**
 * This class consists of static utility methods for operating on values.
 */
final class Values
{

    /**
     * Determines if values should be considered equal.
     *
     * If `$a` implements `Par\Core\Hashable`, `$a->equals($b)` is used, or if `$b` implements `Par\Core\Hashable`
     * `$b->equals($a)` is used, otherwise uses a strict comparison (`$a === $b`).
     *
     * @param mixed $a A value
     * @param mixed $b The referenced value with which to compare
     *
     * @return bool True if the arguments are equal to each other
     * @psalm-mutation-free
     */
    public static function equals(mixed $a, mixed $b): bool
    {
        if ($a instanceof Hashable) {
            return $a->equals($b);
        }

        if ($b instanceof Hashable) {
            return $b->equals($a);
        }

        return $a === $b;
    }

    /**
     * Returns a string representation of the provided value. In general, the `toString` method returns a string that
     * "textually represents" this value. The result should be a concise but informative representation that
     * is easy for a person to read.
     *
     * It will transform a value implementing `\Par\Core\Hashable` to string. Other values become:
     * - `'null'` for a __NULL__ value.
     * - `'value'` for a native __integer__.
     * - `'value'` for a native __float__ or __double__.
     * - `'true'` or `'false'` for a native __boolean__.
     * - `'value'` for a native __string__.
     * - `'[el1, el2, elN]'` for a native __array__ list or `'{key1=el1, key2=el2, keyN=elN}'` for native a __array__
     *   map. Where __elN__ and __keyN__ are textual representations of its value, except when its value is an array
     *   then `'[...]'` is used.
     * - `'className@hash'` for an __object__. `get_class($value)` is used for all objects except for anonymous
     *   classes, in which case "anonymous" is used. The hash is determined via `static::hash($value)`.
     * - `'closure@hash'` for a __closure__. The hash is determined via `static::hash($value)`. Be aware that a closure
     *   in PHP is actually an object (instance of `\Closure`).
     * - `'resource(type)@hash'` for a __resource__. The type is determined via `get_resource_type` unless the resource
     *   is closed, in which case 'closed' is used since the type cannot be determined in PHP. The hash is determined
     *   via `static::hash($value)`.
     *
     * @param mixed $value The value for which to determine the textual representation
     *
     * @return string
     * @psalm-mutation-free
     */
    public static function toString(mixed $value): string
    {
        if (null === $value) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        if ($value instanceof Stringable) {
            return (string)$value;
        }

        if (is_array($value)) {
            return self::arrayToString($value);
        }

        $type = self::typeOf($value);
        $hash = self::hash($value);

        if ($type === 'resource') {
            $resourceType = 'closed';
            if (is_resource($value)) {
                $resourceType = get_resource_type($value);
            }
            $type = sprintf('resource(%s)', $resourceType);
        }

        return sprintf('%s@%s', $type, (string)$hash);
    }

    /**
     * Transform an array to its textual representation.
     *
     * @param array<mixed> $value The array to transform
     *
     * @return string The resulting string
     * @psalm-mutation-free
     */
    private static function arrayToString(array $value): string
    {
        if (array_values($value) === $value) {
            $tpl = '[%s]';
            /**
             * @psalm-suppress ImpureFunctionCall
             */
            $elements = array_map(
                static function ($value): string {
                    if (is_array($value)) {
                        return '[...]';
                    }

                    return self::toString($value);
                },
                $value
            );
        } else {
            $tpl = '{%s}';
            /**
             * @psalm-suppress ImpureFunctionCall
             */
            $elements = array_map(
                static function ($key, $value): string {
                    if (is_array($value)) {
                        $valueString = '[...]';
                    } else {
                        $valueString = self::toString($value);
                    }

                    return sprintf('%s=%s', $key, $valueString);
                },
                array_keys($value),
                $value
            );
        }

        return sprintf($tpl, implode(', ', $elements));
    }

    /**
     * Returns a textual representation for the type of value.
     *
     * - `'null'` for a __NULL__ value.
     * - `'int'` for a native __integer__.
     * - `'float'` for a native __float__ or __double__.
     * - `'bool'` for a native __boolean__.
     * - `'string'` for a native __string__.
     * - `'array'` for a native __array__.
     * - `'className'` for an __object__. `get_class($value)` is used for all objects except for anonymous classes, in
     *   which case 'anonymous' is used.
     * - `'closure'` for a __closure__ which is actually an instance of `Closure`.
     * - `'resource'` for a __resource__.
     *
     * @param mixed $value The value for which to determine the type
     *
     * @return string The type of value.
     * @psalm-mutation-free
     */
    public static function typeOf(mixed $value): string
    {
        if (is_object($value)) {
            return self::getObjectType($value);
        }

        $nativeType = gettype($value);
        $map = [
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'resource' => 'resource',
            'resource (closed)' => 'resource',
            'NULL' => 'null',
            'array' => 'array',
            'string' => 'string',
        ];

        return $map[$nativeType] ?? 'unknown';
    }

    /**
     * Returns the type of object.
     *
     * Instances of `Closure` return `'closure'`, anonymous instances return `'anonymous'` all other instances return
     * `get_class($value)`.
     *
     * @param object $value The object to get the type for.
     *
     * @return string The objects type
     * @psalm-mutation-free
     */
    private static function getObjectType(object $value): string
    {
        if ($value instanceof Closure) {
            return 'closure';
        }

        $class = get_class($value);
        if (preg_match('/^class@anonymous/', $class)) {
            $class = 'anonymous';
        }

        return $class;
    }

    /**
     * Produces a scalar or null value to be used as the value's hash, which determines where it goes in the hash
     * table. While this value does not have to be unique, values which are equal must have the same hash value.
     *
     * @param mixed $value The value to produce a hash for
     *
     * @return bool|float|int|string|null
     * @psalm-mutation-free
     */
    public static function hash(mixed $value): bool|float|int|string|null
    {
        if (is_scalar($value) || null === $value) {
            return $value;
        }

        if ($value instanceof Hashable) {
            return $value->hash();
        }

        return HashCode::forAny($value);
    }
}