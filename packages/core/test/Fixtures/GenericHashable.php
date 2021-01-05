<?php

declare(strict_types=1);

namespace ParTest\Core\Fixtures;

use Par\Core\Hashable;

final class GenericHashable implements Hashable
{
    public function __construct(private int|string|bool|null|float $value)
    {
    }

    public function equals(mixed $other): bool
    {
        if ($other instanceof self) {
            return $this->value === $other->value;
        }

        return false;
    }

    public function hash(): int|string|bool|null|float
    {
        return $this->value;
    }
}
