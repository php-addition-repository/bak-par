<?php

declare(strict_types=1);

namespace Par\CoreTest\Fixtures;

use Par\Core\Hashable;

/**
 * @internal
 */
final class GenericHashable implements Hashable
{
    public function __construct(private int|string|bool|null|float $value)
    {
    }

    /**
     * @param mixed $other
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof self) {
            return $this->value === $other->value;
        }

        return false;
    }

    /**
     * @return int|string|bool|float|null
     * @psalm-mutation-free
     */
    public function hash(): int|string|bool|null|float
    {
        return $this->value;
    }
}
