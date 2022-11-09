<?php

declare(strict_types=1);

namespace Par\Core;

use IntlChar;
use TypeError;

/**
 * Collection of static methods to easily transform a value of any type to an 32-bit integer representation.
 */
final class HashCode
{
    public const MAX_RECURSION_DEPTH = 10;

    /**
     * Transform any value to a hash, recursion safe.
     *
     * @param mixed $value    The value to transform to a hash
     * @param int   $maxDepth The maximum recursion level
     *
     * @return int The resulting hash
     * @psalm-suppress MixedArgument
     * @psalm-pure
     */
    public static function forAny(mixed $value, int $maxDepth = self::MAX_RECURSION_DEPTH): int
    {
        $type = gettype($value);
        return match ($type) {
            'boolean' => self::forBool($value),
            'integer' => self::forInt($value),
            'double' => self::forFloat($value),
            'string' => self::forString($value),
            'array' => self::forArray($value, $maxDepth),
            'object' => self::forObject($value),
            'resource', 'resource (closed)' => self::forResource($value),
            'NULL' => 0,
        };
    }

    /**
     * Transform a boolean to integer hash.
     *
     * @param bool $value The boolean to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forBool(bool $value): int
    {
        return $value ? 1231 : 1237;
    }

    /**
     * Transform an integer to integer hash.
     *
     * @param int $value The integer to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forInt(int $value): int
    {
        $max = 2 ** 31 - 1;
        $min = (2 ** 31) * -1;
        if ($value <= $max && $value >= $min) {
            return $value;
        }

        $hash = ($value ^ ($value >> 32));

        return self::handleOverflow($hash);
    }

    /**
     * Transform a float to integer hash.
     *
     * @param float $value The float to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forFloat(float $value): int
    {
        $packed = pack('g', $value);
        [, $number] = unpack('V', $packed);

        return self::handleOverflow((int)$number);
    }

    /**
     * Transform a string to a hash.
     *
     * @param string $value The string to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forString(string $value): int
    {
        $hash = 0;
        $length = mb_strlen($value);
        for ($i = 0; $i < $length; $i++) {
            $hash = self::handleOverflow(31 * $hash + IntlChar::ord(mb_substr($value, $i, 1)));
        }

        return $hash;
    }

    /**
     * Transform an array to a hash.
     *
     * @param array $values   The array to transform
     * @param int   $maxDepth The maximum recursion depth. Defaults to `static::MAX_RECURSION_DEPTH`
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forArray(array $values, int $maxDepth = self::MAX_RECURSION_DEPTH): int
    {
        if ($maxDepth === 0 || empty($values)) {
            return 0;
        }

        $hashes = array_map(
            static function ($value) use ($maxDepth) {
                return self::forAny($value, $maxDepth - 1);
            },
            $values
        );

        if (array_values($values) !== $values) {
            $hashes[] = self::forArray(array_keys($values), 1);
        }

        return array_reduce(
            $hashes,
            static function (int $previous, int $hash): int {
                return self::handleOverflow($previous + $hash);
            },
            0
        );
    }

    /**
     * Transform an object to integer hash.
     *
     * @param object $value The object to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forObject(object $value): int
    {
        if ($value instanceof Hashable) {
            return self::forAny($value->hash());
        }

        return self::forInt(spl_object_id($value));
    }

    /**
     * Transform an resource to integer hash.
     *
     * @param mixed $value The resource to transform
     *
     * @return int The resulting hash
     * @psalm-pure
     */
    public static function forResource(mixed $value): int
    {
        // PHP does not (yet) support an argument type for resource AND handles closed resource differently.
        $typeName = gettype($value);
        if (!in_array($typeName, ['resource', 'resource (closed)'], true)) {
            throw new TypeError(
                sprintf(
                    'Argument 1 passed to %s() must be of type resource, %s given',
                    __FUNCTION__,
                    $typeName
                )
            );
        }

        return self::forInt((int)$value);
    }

    /**
     * Handles overflowing of an integer
     *
     * @param int $value
     *
     * @return int
     * @psalm-pure
     */
    private static function handleOverflow(int $value): int
    {
        $bits = 32;
        $sign_mask = 1 << $bits - 1;
        $clamp_mask = ($sign_mask << 1) - 1;

        if ($value & $sign_mask) {
            return ((~$value & $clamp_mask) + 1) * -1;
        }

        return $value & $clamp_mask;
    }
}
