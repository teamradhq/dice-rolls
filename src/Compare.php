<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll;

use InvalidArgumentException;
use Teamradhq\DiceRoll\Contracts\Comparison;
use Teamradhq\DiceRoll\Contracts\Rollable;

final class Compare implements Comparison
{
    /**
     * @throws RollException
     */
    public static function guard(mixed $a, mixed $b): void
    {
        foreach ([$a, $b] as $argument) {
            if ($argument instanceof Rollable === false) {
                throw new InvalidArgumentException('All arguments must be an instance of Rollable.');
            }

            if (!$argument->isRolled()) {
                throw new RollException('Both dice must be rolled for comparison.');
            }
        }
    }

    /**
     * @throws RollException
     */
    public function gt(Rollable $a, Rollable $b): bool
    {
        self::guard($a, $b);

        return $a->result() > $b->result();
    }

    /**
     * @throws RollException
     */
    public function gte(Rollable $a, Rollable $b): bool
    {
        self::guard($a, $b);

        return $a->result() >= $b->result();
    }

    /**
     * @throws RollException
     */
    public function lt(Rollable $a, Rollable $b): bool
    {
        self::guard($a, $b);

        return $a->result() < $b->result();
    }

    /**
     * @throws RollException
     */
    public function lte(Rollable $a, Rollable $b): bool
    {
        self::guard($a, $b);

        return $a->result() <= $b->result();
    }

    /**
     * @throws RollException
     */
    public function eq(Rollable $a, Rollable $b): bool
    {
        self::guard($a, $b);

        return $a->result() === $b->result();
    }

    /**
     * @throws RollException
     */
    public function neq(Rollable $a, Rollable $b): bool
    {
        return !$this->eq($a, $b);
    }
}
