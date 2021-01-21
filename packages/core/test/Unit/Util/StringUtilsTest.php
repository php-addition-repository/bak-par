<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\Util;

use Par\Core\Util\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function itCanQuoteAnArrayOfStrings(): void
    {
        $strings = ['a', 'b'];

        self::assertSame(['"a"', '"b"'], StringUtils::quoteList($strings));
    }

    /**
     * @return array<string, array{string, string[]}>
     */
    public function provideHumanReadableStrings(): array
    {
        return [
            'more than two elements' => ['a, b and c', ['a', 'b', 'c']],
            'two elements' => ['a and c', ['a', 'c']],
            'one element' => ['c', ['c']],
            'empty' => ['', []],
        ];
    }

    /**
     * @test
     * @dataProvider provideHumanReadableStrings
     *
     * @param string   $expected
     * @param string[] $strings
     *
     * @return void
     */
    public function itTransformsAnArrayOfStringToHumanReadable(string $expected, array $strings): void
    {
        self::assertSame($expected, StringUtils::listToHumanReadable($strings));
    }
}
