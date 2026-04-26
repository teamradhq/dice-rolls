<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Dice;

interface Randomiser
{
    public function value(int $min = 1, int $max = 6): int;
}
