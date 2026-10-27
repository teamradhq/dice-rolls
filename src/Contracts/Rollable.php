<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\RollException;

interface Rollable
{
    /**
     * This dice has been rolled.
     */
    public function isRolled(): bool;

    /**
     * Get the result of a rolled dice.
     */
    public function result(): int;

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
     * Roll multiple times.
     *
     * @return non-empty-list<Dice>
     *
     * @throws RollException
     */
    public function times(int $times): array;
}
