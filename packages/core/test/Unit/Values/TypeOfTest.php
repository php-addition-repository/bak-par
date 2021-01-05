<?php

declare(strict_types=1);

namespace ParTest\Core\Unit\Values;

use Par\Core\Values;
use ParTest\Core\Traits\ResourceTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TypeOfTest extends TestCase
{
    use ResourceTrait;

    /**
     * @return array<string, array>
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
    public function itCanDetermineTypeOfValue($value, string $expectedType): void
    {
        self::assertSame($expectedType, Values::typeOf($value));
    }
}
