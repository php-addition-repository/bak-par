<?php

declare(strict_types=1);

namespace Par\CoreTest\Fixtures;

use Par\Core\Enum;

/**
 * @internal
 * @psalm-immutable
 * @extends Enum<Planet>
 *
 * @method static self Mercury()
 * @method static self Venus()
 * @method static self Earth()
 * @method static self Mars()
 * @method static self Jupiter()
 * @method static self Saturn()
 * @method static self Uranus()
 * @method static self Neptune()
 */
final class Planet extends Enum
{
    public function mass(): float
    {
        return match ($this) {
            static::Mercury() => 3.303e+23,
            static::Venus() => 4.869e+24,
            static::Earth() => 5.976e+24,
            static::Mars() => 6.421e+23,
            static::Jupiter() => 1.9e+27,
            static::Saturn() => 5.688e+26,
            static::Uranus() => 8.686e+25,
            static::Neptune() => 1.024e+26,
        };
    }
}