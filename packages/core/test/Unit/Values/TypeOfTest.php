<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\Values;

use Par\Core\Values;
use Par\CoreTest\Traits\ResourceTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TypeOfTest extends TestCase
{
    use ResourceTrait;

    /**
     * @return array<string, array{mixed, string}>
     */
    public function providedValuesWithExpectedType(): array
    {
        $obj = new stdClass();

        $resource = $this->createResource();

        $closedResource = $this->createClosedResource();

        return [
            'string' => ['foo', 'string'],
            'int' => [1, 'int'],
            'bool' => [true, 'bool'],
            'null' => [null, 'null'],
            'float' => [0.1, 'float'],
            'array' => [['foo'], 'array'],
            'object' => [$obj, get_class($obj)],
            'closure' => [
                static function () {
                },
                'closure',
            ],
            'resource' => [$resource, 'resource'],
            'resource (closed)' => [$closedResource, 'resource'],
        ];
    }

    /**
     * @test
     * @dataProvider providedValuesWithExpectedType
     *
     * @param mixed  $value
     * @param string $expectedType
     */
    public function itCanDetermineTypeOfValue(mixed $value, string $expectedType): void
    {
        self::assertSame($expectedType, Values::typeOf($value));
    }
}
