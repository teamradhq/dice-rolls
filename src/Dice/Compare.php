<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Dice;

use BadMethodCallException;
use InvalidArgumentException;

/**
 * @method bool gt(Rollable $a, Rollable $b) A is greater than B.
 * @method bool gte(Rollable $a, Rollable $b) A is greater than or equal to B.
 * @method bool lt(Rollable $a, Rollable $b) A is less than B.
 * @method bool lte(Rollable $a, Rollable $b) A is less than or equal to B.
 * @method bool eq(Rollable $a, Rollable $b) A is equal to B.
 * @method bool neq(Rollable $a, Rollable $b) A is not equal to B.
 */
final class Compare
{
    /**
     * @param array{0: Rollable, 1: Rollable} $arguments
     *
     * @throws RollException
     */
    public function __call(string $name, array $arguments): bool
    {
        [$a, $b] = self::guard($arguments);

        return match ($name) {
            'gt' => $a > $b,
            'gte' => $a >= $b,
            'lt' => $a < $b,
            'lte' => $a <= $b,
            'eq' => $a === $b,
            'neq' => $a !== $b,
            default => throw new BadMethodCallException(sprintf('Unknown comparison method: %s', $name)),
        };
    }

    /**
     * @param array<int, mixed> $arguments
     * @return array{0: int, 1: int}
     *
     * @throws RollException
     */
    private static function guard(array $arguments): array
    {
        if (count($arguments) !== 2) {
            throw new InvalidArgumentException('The method must be called with exactly two arguments.');
        }

        foreach ($arguments as $argument) {
            if ($argument instanceof Rollable === false) {
                throw new InvalidArgumentException('The argument must be an instance of Rollable.');
            }

            if (!$argument->isRolled()) {
                throw new RollException('Both dice must be rolled before comparison.');
            }
        }

        /** @var Rollable[] $arguments */
        [$a, $b] = $arguments;

        if ($a->isRolled() && $b->isRolled()) {
            return [$a->result(), $b->result()];
        }

        throw new RollException('Both dice must be rolled before comparison.');
    }
}
