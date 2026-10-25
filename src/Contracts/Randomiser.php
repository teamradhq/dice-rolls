<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

/**
 * Provides a method for deriving a random value within a given range.
 */
interface Randomiser
{
    /**
     * Get a new random value.
     */
    public function value(int $min = 1, int $max = 6): int;
}
