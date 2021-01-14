<?php

declare(strict_types=1);

namespace Par\Core;

use Par\Core\Exception\InvalidEnumDefinition;
use Par\Core\Exception\InvalidEnumElement;
use ReflectionClass;
use Serializable;
use Stringable;

abstract class Enum implements Hashable, Stringable, Serializable
{
    private static array $definitionCache = [];
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

        if (static::$definitionCache[$className] ?? null) {
            return static::$definitionCache[$className];
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
            $definition[$name] = new EnumDefinition($name, $ordinal, $constants[$name]);
        }

        return static::$definitionCache[$className] ??= $definition;
    }

    private static function createFromDefinition(EnumDefinition $definition): static
    {
        $instance = new static(...$definition->args());
        $instance->name = $definition->name();
        $instance->ordinal = $definition->ordinal();

        return $instance;
    }

    /**
     * Returns a list containing the elements of this enum type, in the order they are declared.
     *
     * @return static[]
     */
    final public static function values(): iterable
    {
        $values = [];
        foreach (static::resolveDefinition() as $definition) {
            $values[] = static::createFromDefinition($definition);
        }

        return $values;
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

    public function serialize(): string
    {
        return $this->name;
    }

    public function unserialize($serialized): void
    {
        $className = static::class;

        $definition = static::findDefinition($serialized);
        if (!$definition) {
            throw InvalidEnumElement::withName($className, $serialized);
        }

        $args = $definition->args();
        if (!empty($args)) {
            $reflectionClass = new ReflectionClass($className);

            $constructor = $reflectionClass->getConstructor();
            if ($constructor && $constructor->getParameters()) {
                $constructor->setAccessible(true);
                $constructor->invokeArgs($this, $args);
                $constructor->setAccessible(false);
            }
        }

        $this->name = $serialized;
        $this->ordinal = $definition->ordinal();
    }
}