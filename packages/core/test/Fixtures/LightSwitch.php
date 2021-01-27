<?php

declare(strict_types=1);

namespace Par\CoreTest\Fixtures;

use Par\Core\Enum;

/**
 * @internal
 * @extends Enum<Planet>
 *
 * @method static self Off()
 * @method static self On()
 */
final class LightSwitch extends Enum
{

}   