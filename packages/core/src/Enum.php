<?php

declare(strict_types=1);

namespace Par\Core;

use Par\Core\Exception\InvalidEnumDefinition;
use Par\Core\Exception\InvalidEnumElement;
use ReflectionClass;
use Stringable;

/**
 * This is the common base class of all enumerations.
 *
 * @example "packages/core/test/Fixtures/Planet.php" Implementation example
 */
abstract class Enum implements Hashable, Stringable
{
    private static array $definitionCache = [];
    private static array $allInstancesLoaded = [];
    private static array $instances = [];

    private string $name;
    private int $ordinal;

    protected function __construct()
    {
        // Protected to prevent usage of "new EnumImpl"
    }

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
     */
    private static function resolveDefinition(): array
    {
        $className = static::class;

        if (self::$definitionCache[$className] ?? null) {
            return self::$definitionCache[$className];
        }

        $methods = [];

        /** @noinspection PhpUnhandledExceptionInspection */
        $reflectionClass = new ReflectionClass($className);
        $docComment = $reflectionClass->getDocComment();
        if (is_string($docComment)) {
            preg_match_all('/@method\s+static\s+self\s+([\w_]+)\(\s*?\)/', $docComment, $matches);

            $methods = array_values($matches[1]);
        }

        if (count($methods) === 0) {
            throw InvalidEnumDefinition::noMethodTagsDefinedOn($className);
        }

        $constants = [];
        foreach ($reflectionClass->getReflectionConstants() as $reflectionClassConstant) {
            if ($reflectionClassConstant->isPublic()) {
                continue;
            }
            $value = $reflectionClassConstant->getValue();
            $constants[$reflectionClassConstant->getName()] = is_array($value) ? $value : [];
        }

        // Validate all or none of the methods have a constant value
        $missingConstants = array_diff($methods, array_keys($constants));
        $numMissingConstants = count($missingConstants);
        if ($numMissingConstants > 0 && $numMissingConstants !== count($methods)) {
            throw InvalidEnumDefinition::missingClassConstants($className, $missingConstants);
        }

        $definition = [];
        foreach ($methods as $ordinal => $name) {
            $definition[$name] = new EnumDefinition($name, $ordinal, $constants[$name] ?? []);
        }

        return self::$definitionCache[$className] ??= $definition;
    }

    private static function createFromDefinition(EnumDefinition $definition): static
    {
        $className = static::class;
        if (isset(self::$instances[$className][$definition->name()])) {
            return self::$instances[$className][$definition->name()];
        }

        $instance = new static(...$definition->args());
        $instance->name = $definition->name();
        $instance->ordinal = $definition->ordinal();

        return self::rememberInstance($definition->name(), $instance);
    }

    private static function rememberInstance(string $name, Enum $instance): static
    {
        $className = static::class;
        self::$instances[$className][$name] = $instance;
        if (!isset(self::$allInstancesLoaded[$className])
            && count(self::$instances[$className]) === count(self::$definitionCache[$className])
        ) {
            uasort(
                self::$instances[$className],
                static function (self $a, self $b) {
                    return $a->ordinal() <=> $b->ordinal();
                }
            );

            self::$allInstancesLoaded[$className] = true;
        }

        return $instance;
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
     * Returns a list containing the elements of this enum type, in the order they are declared.
     *
     * @return static[]
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
     * Returns the name of this enum element, exactly as declared.
     */
    final public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    final public function __toString(): string
    {
        return sprintf("%s::%s", static::class, $this->name);
    }

    public function equals(mixed $other): bool
    {
        if ($other instanceof static) {
            return $this->hash() === $other->hash();
        }

        return false;
    }

    public function hash(): bool|float|int|string
    {
        return $this->ordinal;
    }

    public function __clone(): void
    {
        $className = static::class;
        throw new \BadMethodCallException("Cannot clone enum {$className}.");
    }

    public function __sleep(): array
    {
        $className = static::class;
        throw new \BadMethodCallException("Cannot serialize enum {$className}.");
    }

    public function __wakeup(): void
    {
        $className = static::class;
        throw new \BadMethodCallException("Cannot unserialize enum {$className}.");
    }
}