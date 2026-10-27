<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

use Random\RandomException;

/**
 * Provides a method for deriving a random value within a given range.
 */
interface Randomiser
{
    /**
     * Get a new random value.
     *
     * @throws RandomException
     */
    public function value(int $min = 1, int $max = 6): int;
}
