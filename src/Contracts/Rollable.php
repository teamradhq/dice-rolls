<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

interface Rollable
{
    /**
     * The number of sides on the dice.
     */
    public function sides(): int;

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
    public function isRolled(): bool;

    /**
     * Get the result of a rolled dice.
     */
    public function result(): int;
}
