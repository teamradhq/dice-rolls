<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Dice;

interface Rollable
{
    /**
     * Roll the dice.
     */
    public function roll(): self;

    /**
     * Roll with advantage.
     */
    public function advantage(): self;

    /**
     * Roll with disadvantage.
     */
    public function disadvantage(): self;

    /**
     * This dice has been rolled.
     */
    public function rolled(): bool;
}
