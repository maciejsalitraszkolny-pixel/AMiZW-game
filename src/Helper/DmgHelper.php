<?php

declare(strict_types=1);


namespace App\Helper;


use Random\RandomException;

class DmgHelper
{
    /**
     * @throws RandomException
     */
    public static function calculateDamage(int $minDmg, int $maxDmg): int
    {
        return random_int($minDmg, $maxDmg);
    }

    /**
     * @throws RandomException
     */
    public static function calculateHeavyDamage(int $minHeavyDmg, int $maxHeavyDmg): int
    {
        return random_int($minHeavyDmg, $maxHeavyDmg);
    }

    /**
     * @throws RandomException
     */
    public static function calculateRunDamage(int $minRunDamage, int $maxRunDamage): int
    {
        return random_int($minRunDamage, $maxRunDamage);
    }
}
