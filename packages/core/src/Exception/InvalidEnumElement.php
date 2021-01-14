<?php

declare(strict_types=1);

namespace Par\Core\Exception;

final class InvalidEnumElement extends LogicException
{
    public static function withName(string $className, string $elementName): self
    {
        $message = sprintf('Unknown enum element %s::%s', $className, $elementName);

        return new self($message);
    }
}