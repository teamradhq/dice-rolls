<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Dice;

use Random\RandomException;

final readonly class RandomInt implements Randomiser
{
    /**
     * @throws RandomException
     */
    public function value(int $min = 1, int $max = 6): int
    {
        return random_int($min, $max);
    }
}
