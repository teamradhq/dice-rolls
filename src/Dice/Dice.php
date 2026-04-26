<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Dice;

use Random\RandomException;

final class Dice implements Rollable
{
    private const int UNROLLED = -1;

    private static ?Randomiser $randomiser = null;

    /**
     * @throws RollException
     */
    private function __construct(
        public readonly int $sides = 6,
        public readonly int $result = self::UNROLLED,
    ) {
        if ($result !== self::UNROLLED && ($result < 1 || $result > $this->sides)) {
            throw new RollException(sprintf(
                'Out of range: must be between 1 and %d.',
                $this->sides
            ));
        }
    }

    /**
     * @throws RollException
     */
    public static function make(int $sides = 6): self
    {
        return new self($sides);
    }

    public static function setRandomiser(?Randomiser $randomiser): void
    {
        self::$randomiser = $randomiser;
    }

    /**
     * @throws RollException
     */
    public function roll(): self
    {
        try {
            return new self(
                $this->sides,
                self::randomiser()->value(1, $this->sides)
            );
        } catch (RandomException $randomException) {
            throw new RollException(
                'Could not roll the dice.',
                $randomException->getCode(),
                previous: $randomException
            );
        }
    }

    public function rolled(): bool
    {
        return $this->result !== self::UNROLLED;
    }

    /**
     * @throws RollException
     */
    public function advantage(): self
    {
        $rolls = [$this->roll(), $this->roll()];

        if ($rolls[0]->result > $rolls[1]->result) {
            return $rolls[0];
        }

        return $rolls[1];
    }

    /**
     * @throws RollException
     */
    public function disadvantage(): self
    {
        return $this->roll();
    }

    /**
     * @throws RollException
     */
    private static function randomiser(): Randomiser
    {
        if (is_null(self::$randomiser)) {
            self::setRandomiser(new RandomInt);
        }

        if (self::$randomiser instanceof Randomiser === false) {
            throw new RollException('Randomiser must be an instance of RandomInt.');
        }

        return self::$randomiser;
    }
}
