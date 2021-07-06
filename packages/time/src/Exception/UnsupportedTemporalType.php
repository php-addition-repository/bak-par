<?php

declare(strict_types=1);

namespace Par\Time\Exception;

use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\TemporalUnit;
use RuntimeException;

final class UnsupportedTemporalType extends RuntimeException implements ExceptionInterface
{
    public static function forField(TemporalField $field): static
    {
        return new static(
            sprintf(
                'Unsupported field: %s',
                $field->toString()
            )
        );
    }

    public static function forUnit(TemporalUnit $unit): static
    {
        return new static(
            sprintf(
                'Unsupported unit: %s',
                $unit->toString()
            )
        );
    }
}