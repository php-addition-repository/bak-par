<?php

declare(strict_types=1);

namespace Par\Core;

/**
 * @internal
 * @psalm-immutable
 */
final class EnumDefinition
{
    public function __construct(private string $name, private int $ordinal, private array $args)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function ordinal(): int
    {
        return $this->ordinal;
    }

    public function args(): array
    {
        return $this->args;
    }

    public function isFor(string $name): bool
    {
        return $this->name === $name;
    }

}