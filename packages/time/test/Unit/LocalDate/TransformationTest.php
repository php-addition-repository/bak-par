<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\LocalDate;

use Par\Time\Factory;
use Par\Time\LocalDate;
use Par\Time\PHPUnit\TimeTestCaseTrait;
use PHPUnit\Framework\TestCase;

final class TransformationTest extends TestCase
{
    use TimeTestCaseTrait;

    /**
     * @dataProvider provideForStringTransformation
     *
     * @param LocalDate $source
     * @param string    $expectedString
     *
     * @return void
     */
    public function testItCanBeTransformedToString(LocalDate $source, string $expectedString): void
    {
        self::assertSame($expectedString, $source->toString());
    }

    /**
     * @return array<string, array{LocalDate, string}>
     */
    public function provideForStringTransformation(): array
    {
        return [
            'single-digit-month' => [LocalDate::of(2011, 2, 10), '2011-02-10'],
            'single-digit-year' => [LocalDate::of(2011, 11, 2), '2011-11-02'],
        ];
    }

    public function testItCanBeTransformedToNative(): void
    {
        $source = LocalDate::of(2011, 2, 10);

        $expected = Factory::createDate(2011, 2, 10);
        self::assertEquals($expected, $source->toNative());
    }
}