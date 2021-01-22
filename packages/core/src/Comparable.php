<?php

declare(strict_types=1);

namespace Par\Core;

use Par\Core\Exception\ClassMismatch;

/**
 * This interface imposes a total ordering on the objects of each class that implements it. This ordering is referred
 * to as the class's natural ordering, and the class's compareTo method is referred to as its natural comparison
 * method.
 *
 * @template T
 */
interface Comparable
{
    /**
     * Compares this object with the specified object for order. Returns a negative integer, zero, or a positive
     * integer as this object is less than, equal to, or greater than the specified object.
     *
     * @param T $other The object to be compared.
     *
     * @return int A negative integer, zero, or a positive integer as this object is less than, equal to, or greater
     *             than the specified value.
     * @throws ClassMismatch If the specified object's type prevents it from being compared to this object
     */
    public function compareTo(Comparable $other): int;
}