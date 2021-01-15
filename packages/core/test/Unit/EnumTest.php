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
        $element = Planet::valueOf('Earth');

        self::assertInstanceOf(Planet::class, $element);
        self::assertSame('Earth', $element->name());
    }

    /**
     * @test
     */
    public function itCanBeCastToString(): void
    {
        $element = Planet::Earth();

        self::assertSame(Planet::class . '::Earth', (string)$element);
    }

    /**
     * @test
     */
    public function itCanBeTransformedToString(): void
    {
        $element = Planet::Earth();

        self::assertSame('Earth', $element->toString());
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
        // Because of internal caching we need to fetch the list first
        $values = Planet::values();

        self::assertSame(
            [
                Planet::Mercury(),
                Planet::Venus(),
                Planet::Earth(),
                Planet::Mars(),
                Planet::Jupiter(),
                Planet::Saturn(),
                Planet::Uranus(),
                Planet::Neptune(),
            ],
            $values
        );
    }

    /**
     * @test
     */
    public function itsNameEqualsDefinedMethodTag(): void
    {
        self::assertSame('Mars', Planet::Mars()->name());
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
    public function itCannotUsedInSerialization(): void
    {
        $this->expectException(\BadMethodCallException::class);

        serialize(Planet::Earth());
    }

    /**
     * @test
     */
    public function itCannotBeCloned(): void
    {
        $this->expectException(\BadMethodCallException::class);

        clone Planet::Earth();
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

        $instances = $reflectionClass->getProperty('instances');
        $instances->setAccessible(true);
        $instances->setValue($instances->getDefaultValue());

        $allInstancesLoaded = $reflectionClass->getProperty('allInstancesLoaded');
        $allInstancesLoaded->setAccessible(true);
        $allInstancesLoaded->setValue($allInstancesLoaded->getDefaultValue());
    }
}

/**
 * @internal
 */
final class NoMethodTagsEnum extends Enum
{
}