<?php

declare(strict_types=1);

namespace Par\Core\Util;

use Stringable;

/**
 * @internal
 */
final class StringUtils
{
    /**
     * Returns a list where all elements have been encapsulated with double quotes (`"element"`).
     *
     * @param array<string|Stringable> $list A list of string that will be quoted
     *
     * @return array<string> The quoted list of strings
     * @psalm-pure
     */
    public static function quoteList(array $list): array
    {
        return array_map(
            static function (string|Stringable $string) {
                return sprintf('"%s"', $string);
            },
            $list
        );
    }

    /**
     * Returns a string where all elements have been joined in a human-readable way.
     *
     * For example:
     * - `['a','b','c']` becomes `"a, b and c"`.
     * - `['a','b']` becomes `"a and b"`.
     * - `['a']` becomes `"a"`.
     *
     * @param array<string|Stringable> $list A list of strings
     *
     * @return string A string where all elements have been joined in a human-readable way
     * @psalm-pure
     */
    public static function listToHumanReadable(array $list): string
    {
        if (count($list) === 0) {
            return '';
        }

        if (count($list) === 1) {
            return (string)reset($list);
        }

        $lastString = (string)array_pop($list);

        return sprintf('%s and %s', implode(', ', $list), $lastString);
    }
}
