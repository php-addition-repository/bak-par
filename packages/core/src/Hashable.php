<?php

declare(strict_types=1);

namespace Par\Core;

interface Hashable extends \Ds\Hashable
{

    /**
     * Produces a scalar or null value to be used as the object's hash, which determines
     * where it goes in the hash table. While this value does not have to be
     * unique, objects which are equal must have the same hash value.
     *
     * @return bool|float|int|string|null
     */
    public function hash(): bool|float|int|string|null;

    /**
     * Determines if two objects should be considered equal. Both objects will be instances of the same class but may
     * not be the same instance.
     *
     * @example      "packages/core/test/Fixtures/GenericHashable.php" 16 Implementation example
     *
     * @param mixed $other The referenced value with which to compare
     *
     * @return bool True if this object is the same as the other argument
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     */
    public function equals(mixed $other): bool;
}