<?php
/** @noinspection PhpHierarchyChecksInspection */

declare(strict_types=1);

namespace Par\Time;

use Par\Time\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class Assert extends \Webmozart\Assert\Assert
{
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }

}