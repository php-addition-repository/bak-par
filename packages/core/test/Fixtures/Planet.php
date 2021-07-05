<?php

declare(strict_types=1);

namespace Par\CoreTest\Fixtures;

use Par\Core\Enum;

/**
 * @internal
 * @extends Enum<Planet>
 *
 * @method static static Mercury()
 * @method static static Venus()
 * @method static static Earth()
 * @method static static Mars()
 * @method static static Jupiter()
 * @method static static Saturn()
 * @method static static Uranus()
 * @method static static Neptune()
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
