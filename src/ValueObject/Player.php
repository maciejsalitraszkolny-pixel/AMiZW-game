<?php

declare(strict_types=1);


namespace App\ValueObject;


class Player
{
    public function __construct(private int $hp)
    {
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function takeDmg(int $dmg): void
    {
        $this->hp -= $dmg;
    }
    public function setHp(int $hp): void
    {
        $this->hp = $hp;
    }
    public function heal(int $heal): void
    {
        $this->hp += $heal;
    }
    public function run(int $rundmg): void
    {
        $this->hp -= $rundmg;
    }

}
