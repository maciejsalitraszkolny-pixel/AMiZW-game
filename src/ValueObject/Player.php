<?php

declare(strict_types=1);


namespace App\ValueObject;


class Player
{
    public const LEVEL = [
        1 => 100,
        2 => 250,
        3 => 500,
        4 => 1100,
        5 => 2500
    ];
    public function __construct(private int $hp, private int $level, private int $experience)
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
    public function setHeal(int $hp): void
    {
        $this->hp += $hp;
    }
    public function run(): void
    {
        $this->hp -= 10;
    }
    public function addExperience(int $exp): void 
    {
        $this->experience += $exp;
    }
    public function levelUp(): void 
    {
        $this->level++;
    }
    public function getExp(): int
    {
        return $this->experience;
    }
}
