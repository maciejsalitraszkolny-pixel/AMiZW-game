<?php

declare(strict_types=1);


namespace App\Helper;


use Random\RandomException;
use App\ValueObject\Player;

class PlayerHelper
{
    /**
     * @throws RandomException
     */
    public static function checkExperience(int $experience): bool
    {
        foreach(Player::LEVEL as $kay=>$needExperience)
        {
            if($experience<$needExperience)
            {
                return true;
            }
        }
        return false;
    }
}