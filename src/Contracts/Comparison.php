<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

interface Comparison
{
    /**
     *  A is greater than B.
     */
    public function gt(Rollable $a, Rollable $b): bool;

    /**
     *  A is greater than or equal to B.
     */
    public function gte(Rollable $a, Rollable $b): bool;

    /**
     *  A is less than B.
     */
    public function lt(Rollable $a, Rollable $b): bool;

    /**
     *  A is less than or equal to B.
     */
    public function lte(Rollable $a, Rollable $b): bool;

    /**
     *  A is equal to B.
     */
    public function eq(Rollable $a, Rollable $b): bool;

    /**
     *  A is not equal to B.
     */
    public function neq(Rollable $a, Rollable $b): bool;
}
