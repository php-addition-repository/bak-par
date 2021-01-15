<?php

declare(strict_types=1);

namespace ParTest\Core\Unit\Values;

use Par\Core\Values;
use ParTest\Core\Traits;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

final class ToStringTest extends TestCase
{
    use Traits\ResourceTrait;

    /**
     * @test
     */
    public function itCanDetermineStringRepresentationOfStringable(): void
    {
        $expected = 'custom';

        $hashable = $this->createMock(Stringable::class);
        $hashable->expects(self::once())
                 ->method('__toString')
                 ->with()
                 ->willReturn($expected);

        self::assertEquals($expected, Values::toString($hashable));
    }

    /**
     * @return array<string, array{mixed, string}>
     */
    public function provideNativeValuesWithStringRepresentation(): array
    {
        $obj = new stdClass();

        $anonObj = new class () {

        };

        $resource = $this->createResource();

        $closedResource = $this->createClosedResource();

        $closure = static function (): void {
        };

        return [
            'string' => ['foo', 'foo'],
            'int' => [1, '1'],
            'bool' => [true, 'true'],
            'null' => [null, 'null'],
            'float' => [0.1, '0.1'],
            'array-list' => [
                ['foo', 'bar'],
                '[foo, bar]',
            ],
            'array-list-recursive' => [
                ['foo', ['bar']],
                '[foo, [...]]',
            ],
            'array-map' => [
                [1 => 'foo', 3 => 'bar'],
                '{1=foo, 3=bar}',
            ],
            'array-map-recursive' => [
                [1 => ['foo']],
                '{1=[...]}',
            ],
            'object' => [
                $obj,
                sprintf('stdClass@%s', (string)Values::hash($obj)),
            ],
            'anonymous-object' => [
                $anonObj,
                sprintf('anonymous@%s', (string)Values::hash($anonObj)),
            ],
            'closure' => [
                $closure,
                sprintf('closure@%s', (string)Values::hash($closure)),
            ],
            'resource' => [
                $resource,
                sprintf('resource(stream)@%s', (string)Values::hash($resource)),
            ],
            'resource(closed)' => [
                $closedResource,
                sprintf('resource(closed)@%s', (string)Values::hash($closedResource)),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider provideNativeValuesWithStringRepresentation
     *
     * @param mixed  $value
     * @param string $expectedString
     */
    public function itCanTransformNativeValueToString(mixed $value, string $expectedString): void
    {
        self::assertEquals($expectedString, Values::toString($value));
    }
}
