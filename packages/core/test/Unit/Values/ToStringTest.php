<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\Values;

use Par\Core\Values;
use Par\CoreTest\Traits;
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

        $stringFormat = '%s@%s';

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
                sprintf($stringFormat, get_debug_type($obj), (string)Values::hash($obj)),
            ],
            'anonymous-object' => [
                $anonObj,
                sprintf($stringFormat, get_debug_type($anonObj), (string)Values::hash($anonObj)),
            ],
            'closure' => [
                $closure,
                sprintf($stringFormat, get_debug_type($closure), (string)Values::hash($closure)),
            ],
            'resource' => [
                $resource,
                sprintf($stringFormat, get_debug_type($resource), (string)Values::hash($resource)),
            ],
            'resource(closed)' => [
                $closedResource,
                sprintf($stringFormat, get_debug_type($closedResource), (string)Values::hash($closedResource)),
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
