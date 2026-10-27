<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

interface Modifier
{
    /**
     * Get the value for the modifier.
     */
    public function value(): int;

    /**
     * Indicates a positive modifier.
     */
    public function isAdvantage(): bool;

    /**
     * Indicates a negative modifier.
     */
    public function isDisadvantage(): bool;
}
