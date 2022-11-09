<?php

declare(strict_types=1);

namespace Par\Core;

use Stringable;

/**
 * This class consists of static utility methods for operating on values.
 */
final class Values
{
    /**
     * Determines if values should be considered equal.
     *
     * If `$a` implements `Par\Core\ObjectEquality`, `$a->equals($b)` is used, or if `$b` implements
     * `Par\Core\ObjectEquality`
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
        if ($a instanceof ObjectEquality) {
            return $a->equals($b);
        }

        if ($b instanceof ObjectEquality) {
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

        $type = get_debug_type($value);
        $hash = self::hash($value);

        return sprintf('%s@%s', $type, (string)$hash);
    }

    /**
     * Produces a scalar or null value to be used as the value's hash, which determines where it goes in the hash
     * table. While this value does not have to be unique, values which are equal must have the same hash value.
     *
     * @param mixed $value The value to produce a hash for
     *
     * @return bool|float|int|string|null
     *
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

    /**
     * Transform an array to its textual representation.
     *
     * @param array $value The array to transform
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
}
