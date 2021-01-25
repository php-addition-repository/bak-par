<?php

declare(strict_types=1);

namespace Par\Time\Temporal;

use Par\Time\Exception\UnsupportedTemporalType;

/**
 * Framework-level interface defining read-only access to a temporal object, such as a date, time, offset or some
 * combination of these.
 *
 * @internal
 */
interface TemporalAccessor
{
    /**
     * Checks if the specified field is supported.
     *
     * @param TemporalField $field The field to check
     *
     * @return bool
     * @psalm-mutation-free
     */
    public function supportsField(TemporalField $field): bool;

    /**
     * Gets the value of the specified field as an int.
     *
     * @param TemporalField $field The field to get
     *
     * @return int
     * @throws UnsupportedTemporalType
     */
    public function get(TemporalField $field): int;
}