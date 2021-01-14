<?php

declare(strict_types=1);

namespace Par\Core\Exception;

use Par\Core\Util\StringUtils;

final class InvalidEnumDefinition extends LogicException
{
    /**
     * @param string   $className        Fully qualified classname of the invalid enum
     * @param string[] $missingConstants List of missing constant names
     *
     * @return static
     */
    public static function missingClassConstants(string $className, array $missingConstants): self
    {
        $message = sprintf(
            'All enum %s element constants must be declared with private or protected visibility, %s %s missing or have the wrong visibility.',
            $className,
            count($missingConstants) === 1 ? 'is' : 'are',
            StringUtils::listToHumanReadable(StringUtils::quoteList($missingConstants))
        );

        return new self($message);
    }

    public static function noMethodTagsDefinedOn(string $className): self
    {
        $message = sprintf('There are no method tags defined on enum %s.', $className);

        return new self($message);
    }
}