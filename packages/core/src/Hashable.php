<?php

declare(strict_types=1);

namespace Par\Core;

/**
 * Hashable is an interface which allows objects to be used as keys.
 *
 * It's an alternative to spl_object_hash(), which determines an object's hash based on its handle: this means
 * that two objects that are considered equal by an implicit definition would not be treated as equal because they are
 * not the same instance.
 */
interface Hashable extends \Ds\Hashable
{

    /**
     * Produces a scalar or null value to be used as the object's hash, which determines
     * where it goes in the hash table. While this value does not have to be
     * unique, objects which are equal must have the same hash value.
     *
     * @return bool|float|int|string|null
     * @psalm-mutation-free
     */
    public function hash(): bool|float|int|string|null;

    /**
     * Determines if two objects should be considered equal. Both objects will be instances of the same class but may
     * not be the same instance.
     *
     * @example              "packages/core/test/Fixtures/GenericHashable.php" 16 Implementation example
     *
     * @param mixed $obj The referenced value with which to compare
     *
     * @return bool True if this object is the same as the other argument
     * @psalm-mutation-free
     * @psalm-assert-if-true static $obj
     */
    public function equals(mixed $obj): bool;
}
