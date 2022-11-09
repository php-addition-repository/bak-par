<?php

declare(strict_types=1);

namespace Par\CoreTest\Fixtures;

use Par\Core\ObjectEquality;

/**
 * @internal
 * @psalm-immutable
 */
final class ScalarValueObject implements ObjectEquality
{
    public function equals(mixed $other): bool
    {
        if ($other instanceof self) {
            return $other->value === $this->value;
        }

        return false;
    }

    /**
     * @param scalar|null $value
     */
    public function __construct(private readonly float|bool|int|string|null $value)
    {
    }
}
