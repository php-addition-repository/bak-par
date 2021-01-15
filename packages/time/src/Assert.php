<?php

declare(strict_types=1);

namespace Par\Time;

use Par\Time\Exception\InvalidArgumentException;

/**
 * @internal
 * @noinspection PhpHierarchyChecksInspection
 */
final class Assert extends \Webmozart\Assert\Assert
{
    /**
     * @param string $message
     *
     * @return void
     * @psalm-pure
     */
    protected static function reportInvalidArgument($message): void
    {
        throw new InvalidArgumentException($message);
    }

}