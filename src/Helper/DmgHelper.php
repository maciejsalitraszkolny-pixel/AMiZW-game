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
    public static function calculateDmgWithLevel(int $minDmg, int $maxDmg, int $level): int
    {
        return random_int($minDmg+(2*$level), $maxDmg+(2*$level));
    }
}
