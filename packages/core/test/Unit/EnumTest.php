<?php

declare(strict_types=1);

namespace ParTest\Core\Unit;

use Par\Core\Enum;
use Par\Core\Exception\InvalidEnumDefinition;
use Par\Core\Exception\InvalidEnumElement;
use Par\Core\PHPUnit\HashableAssertions;
use ParTest\Core\Fixtures\Planet;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EnumTest extends TestCase
{
    use HashableAssertions;

    /**
     * @test
     */
    public function itCanFindAnElementByName(): void
    {
        $element = Planet::valueOf('earth');

        self::assertInstanceOf(Planet::class, $element);
        self::assertSame('earth', $element->name());
    }

    /**
     * @test
     */
    public function itCanBeCastToString(): void
    {
        $element = Planet::Earth();

        self::assertSame(Planet::class . '::earth', (string)$element);
    }

    /**
     * @test
     */
    public function itCanBeTransformedToString(): void
    {
        $element = Planet::Earth();

        self::assertSame('earth', $element->toString());
    }

    /**
     * @test
     */
    public function itCanDetermineEqualityWithOtherValues(): void
    {
        $element = Planet::Earth();
        $otherElement = Planet::Neptune();

        self::assertHashEquals($element, $element);

        self::assertHashNotEquals($element, $otherElement);
        self::assertHashNotEquals($element, null);
        self::assertHashNotEquals($element, 'earth');
        self::assertHashNotEquals($element, $this);
    }

    /**
     * @test
     */
    public function itCanReturnAllElements(): void
    {
        $expectedValues = [
            Planet::Mercury(),
            Planet::Venus(),
            Planet::Earth(),
            Planet::Mars(),
            Planet::Jupiter(),
            Planet::Saturn(),
            Planet::Uranus(),
            Planet::Neptune(),
        ];
        foreach (Planet::values() as $planet) {
            self::assertHashEquals(array_shift($expectedValues), $planet);
        }
    }

    /**
     * @test
     */
    public function itsNameEqualsDefinedMethodTag(): void
    {
        self::assertSame('mars', Planet::Mars()->name());
    }

    /**
     * @test
     */
    public function itsOrdinalEqualsPositionOfMethodTag(): void
    {
        self::assertSame(3, Planet::Mars()->ordinal());
    }

    /**
     * @test
     */
    public function itPassesCustomValuesOfPrivateConstantsToConstructor(): void
    {
        self::assertSame(1.9e+27, Planet::Jupiter()->mass());
    }

    /**
     * @test
     */
    public function itSupportsUnserialization(): void
    {
        $planet = Planet::Mars();
        $serialized = 'C:28:"ParTest\Core\Fixtures\Planet":4:{mars}';

        /** @var Planet $deserialized */
        $deserialized = unserialize($serialized);

        self::assertInstanceOf(Planet::class, $deserialized);
        self::assertSame($planet->name(), $deserialized->name());
    }

    /**
     * @test
     */
    public function itWillThrowAnExceptionWhenUnserializingNonExistingElement(): void
    {
        $serialized = 'C:28:"ParTest\Core\Fixtures\Planet":3:{sun}';

        $this->expectExceptionObject(
            InvalidEnumElement::withName(Planet::class, 'sun')
        );

        unserialize($serialized);
    }

    /**
     * @test
     */
    public function itSupportsSerialization(): void
    {
        $serialized = serialize(Planet::Earth());

        self::assertSame('C:28:"ParTest\Core\Fixtures\Planet":5:{earth}', $serialized);
    }

    /**
     * @test
     */
    public function itWillThrowAnExceptionWhenNotAllElementsHaveConstants(): void
    {
        $this->expectExceptionObject(
            InvalidEnumDefinition::missingClassConstants(MissingElementConstantsEnum::class, ['second', 'fourth'])
        );

        MissingElementConstantsEnum::values();
    }

    /**
     * @test
     */
    public function itWillThrowAnExceptionWhenNoMethodTagsDefined(): void
    {
        $this->expectExceptionObject(
            InvalidEnumDefinition::noMethodTagsDefinedOn(NoMethodTagsEnum::class)
        );

        NoMethodTagsEnum::values();
    }

    /**
     * @test
     */
    public function itWillThrowAnExceptionWhenElementDoesNotExist(): void
    {
        $this->expectExceptionObject(InvalidEnumElement::withName(Planet::class, 'sun'));

        Planet::valueOf('sun');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Reset all static properties
        $reflectionClass = new ReflectionClass(Enum::class);

        $definitionCache = $reflectionClass->getProperty('definitionCache');
        $definitionCache->setAccessible(true);
        $definitionCache->setValue($definitionCache->getDefaultValue());
    }
}

/**
 * @internal
 *
 * @method static self first()
 * @method static self second()
 * @method static self third()
 * @method static self fourth()
 */
final class MissingElementConstantsEnum extends Enum
{
    // public is ignored
    public const second = [];

    // these are used
    protected const third = [];
    private const first = [];
}

/**
 * @internal
 */
final class NoMethodTagsEnum extends Enum
{
}