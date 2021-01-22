<?php

declare(strict_types=1);

namespace Par\Core;

use BadMethodCallException;
use Par\Core\Exception\InvalidEnumDefinition;
use Par\Core\Exception\InvalidEnumElement;
use ReflectionClass;
use ReflectionException;
use Stringable;
use TypeError;

/**
 * This is the common base class of all enumerations.
 *
 * @example                                   "packages/core/test/Fixtures/Planet.php" Implementation example
 * @psalm-immutable
 *
 * @template-covariant                        T of Enum
 */
abstract class Enum implements Hashable, Stringable, Comparable
{
    /**
     * @var array<string, array<string, EnumDefinition>>
     */
    private static array $definitionCache = [];

    /**
     * @var array<string, bool>
     */
    private static array $allInstancesLoaded = [];

    /**
     * @psalm-var array<string, array<string, Enum>>
     */
    private static array $instances = [];

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return static
     * @internal
     */
    final public static function __callStatic(string $name, array $arguments): static
    {
        return static::valueOf($name);
    }

    /**
     * Returns the enum element of the specified enum type with the specified name. The name must match exactly an
     * identifier used to declare an enum element in this type. (Extraneous whitespace characters are not permitted.)
     *
     * @param string $name The name of the element to return
     *
     * @return static
     * @psalm-pure
     */
    final public static function valueOf(string $name): static
    {
        $definition = static::findDefinition($name);

        if (!$definition) {
            $enumClass = static::class;
            throw InvalidEnumElement::withName($enumClass, $name);
        }

        return static::createFromDefinition($definition);
    }

    /**
     * Returns a list containing the elements of this enum type, in the order they are declared.
     *
     * @return static[]
     * @psalm-mutation-free
     * @psalm-suppress ImpureStaticProperty
     * @psalm-suppress ImpureMethodCall
     */
    final public static function values(): iterable
    {
        $className = static::class;
        if (isset(self::$allInstancesLoaded[$className])) {
            return array_values(self::$instances[$className]);
        }

        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = [];
        }

        foreach (static::resolveDefinition() as $definition) {
            static::createFromDefinition($definition);
        }

        return array_values(self::$instances[$className]);
    }

    /**
     * @param string $name
     *
     * @return EnumDefinition|null
     * @psalm-pure
     */
    private static function findDefinition(string $name): ?EnumDefinition
    {
        foreach (static::resolveDefinition() as $definition) {
            if ($definition->isFor($name)) {
                return $definition;
            }
        }

        return null;
    }

    /**
     * @return array<string, EnumDefinition>
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     * @psalm-suppress ImpureMethodCall
     */
    private static function resolveDefinition(): array
    {
        $className = static::class;

        if (self::$definitionCache[$className] ?? null) {
            return self::$definitionCache[$className];
        }

        $methods = [];

        try {
            $reflectionClass = new ReflectionClass($className);
            $docComment = $reflectionClass->getDocComment();
            if (is_string($docComment)) {
                preg_match_all('/@method\s+static\s+self\s+([\w_]+)\(\s*?\)/', $docComment, $matches);

                $methods = array_values($matches[1]);
            }
        } catch (ReflectionException) {
        }

        if (count($methods) === 0) {
            throw InvalidEnumDefinition::noMethodTagsDefinedOn($className);
        }

        $definition = [];
        foreach ($methods as $ordinal => $name) {
            $definition[$name] = new EnumDefinition($name, $ordinal, []);
        }

        return self::$definitionCache[$className] ??= $definition;
    }

    /**
     * @param EnumDefinition $definition
     *
     * @return static
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     */
    private static function createFromDefinition(EnumDefinition $definition): static
    {
        $className = static::class;

        /** @var T $instance */
        $instance = self::$instances[$className][$definition->name()] ?? new static(
                $definition->ordinal(),
                $definition->name()
            );

        return self::rememberInstance($definition->name(), $instance);
    }

    /**
     * @param string $name
     * @param static $instance
     *
     * @return static
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     */
    private static function rememberInstance(string $name, Enum $instance): static
    {
        $className = static::class;

        self::$instances[$className][$name] = $instance;

        if (!isset(self::$allInstancesLoaded[$className])
            && count(self::$instances[$className]) === count(self::$definitionCache[$className])
        ) {
            uasort(
                self::$instances[$className],
                static function (Enum $a, Enum $b): int {
                    return $a->compareTo($b);
                }
            );

            self::$allInstancesLoaded[$className] = true;
        }

        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function compareTo(Comparable $other): int
    {
        if ($other instanceof static) {
            return $this->ordinal <=> $other->ordinal;
        }

        throw new TypeError(
            sprintf(
                '%s(): Argument #1 ($other) must be of type %s, %s given.',
                __METHOD__,
                static::class,
                get_class($other)
            )
        );
    }

    /**
     * Returns the ordinal of this enum element (its position in its declaration, where the initial element is assigned
     * an ordinal of zero).
     */
    final public function ordinal(): int
    {
        return $this->ordinal;
    }

    /**
     * Returns the name of this enum element, exactly as declared.
     */
    final public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the name of this enum constant, as contained in the declaration.
     *
     * @return string The name of this enum constant
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * Returns the name of this enum constant, as contained in the declaration.
     *
     * @return string The name of this enum constant
     */
    final public function __toString(): string
    {
        return sprintf("%s::%s", static::class, $this->name);
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true T $other
     */
    final public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->hash() === $other->hash();
        }

        return false;
    }

    /**
     * @inheritDoc
     *
     * @return int
     */
    final public function hash(): int
    {
        return $this->ordinal;
    }

    /**
     * @internal
     */
    final public function __clone()
    {
        $className = static::class;
        throw new BadMethodCallException("Cannot clone enum {$className}.");
    }

    /**
     * @internal
     */
    final public function __sleep(): array
    {
        $className = static::class;
        throw new BadMethodCallException("Cannot serialize enum {$className}.");
    }

    /**
     * @internal
     */
    final public function __wakeup(): void
    {
        $className = static::class;
        throw new BadMethodCallException("Cannot unserialize enum {$className}.");
    }

    final private function __construct(private int $ordinal, private string $name)
    {
        // Protected to prevent usage of "new EnumImpl"
    }
}