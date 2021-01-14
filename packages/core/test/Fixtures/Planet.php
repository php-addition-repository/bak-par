<?php

declare(strict_types=1);

namespace ParTest\Core\Fixtures;

use Par\Core\Enum;

/**
 * @internal
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
    private const Mercury = [3.303e+23];
    private const Venus = [4.869e+24];
    private const Earth = [5.976e+24];
    private const Mars = [6.421e+23];
    private const Jupiter = [1.9e+27];
    private const Saturn = [5.688e+26];
    private const Uranus = [8.686e+25];
    private const Neptune = [1.024e+26];

    protected function __construct(private float $mass)
    {
        parent::__construct();
    }

    public function mass(): float
    {
        return $this->mass;
    }
}