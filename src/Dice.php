<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll;

use Random\RandomException;
use Teamradhq\DiceRoll\Contracts\Randomiser;
use Teamradhq\DiceRoll\Contracts\Rollable;

final class Dice implements Rollable
{
    private const int UNROLLED = -1;

    private static ?Randomiser $randomiser = null;

    private static ?Compare $compare = null;

    /**
     * @throws RollException
     */
    private function __construct(
        private readonly int $sides = 6,
        private readonly int $result = self::UNROLLED,
    ) {
        if ($result !== self::UNROLLED && ($result < 1 || $result > $this->sides)) {
            throw new RollException(sprintf(
                'Out of range: must be between 1 and %d.',
                $this->sides
            ));
        }
    }

    public static function setUp(
        Randomiser|false|null $randomiser = false,
        Compare|false|null $compare = false,
    ): void {
        if ($randomiser === null || $randomiser instanceof Randomiser) {
            self::$randomiser = $randomiser;
        }

        if ($compare === null || $compare instanceof Compare) {
            self::$compare = $compare;
        }
    }

    /**
     * @throws RollException
     */
    public static function make(int $sides = 6): self
    {
        return new self($sides);
    }

    /**
     * {@inheritDoc}
     */
    public function isRolled(): bool
    {
        return $this->result !== self::UNROLLED;
    }

    /**
     * {@inheritDoc}
     */
    public function result(): int
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function sides(): int
    {
        return $this->sides;
    }

    /**
     * {@inheritDoc}
     *
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

    /**
     * {@inheritDoc}
     *
     * @throws RollException
     */
    public function advantage(): self
    {
        [$a, $b] = $this->times(2);

        return self::compare()->gte($a, $b) ? $a : $b;
    }

    /**
     * {@inheritDoc}
     *
     * @throws RollException
     */
    public function disadvantage(): self
    {
        [$a, $b] = $this->times(2);

        return self::compare()->lte($a, $b) ? $a : $b;
    }

    /**
     * Roll the dice multiple times.
     *
     * @return non-empty-list<Dice>
     *
     * @throws RollException
     */
    public function times(int $times): array
    {
        if ($times < 1) {
            throw new RollException('Dice must be rolled at least once.');
        }

        /** @var non-empty-list<Dice> $rolls */
        $rolls = [];

        for ($i = 0; $i < $times; $i++) {
            $rolls[] = $this->roll();
        }

        return $rolls;
    }

    private static function randomiser(): Randomiser
    {
        if (!self::$randomiser instanceof Randomiser) {
            self::setUp(new RandomInt);
        }

        /** @phpstan-ignore return.type (Setup call ensures property is not null.) */
        return self::$randomiser;
    }

    private static function compare(): Compare
    {
        if (self::$compare instanceof Compare === false) {
            self::setUp(compare: new Compare);
        }

        /** @phpstan-ignore return.type (Setup call ensures property is not null.) */
        return self::$compare;
    }
}
