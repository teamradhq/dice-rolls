<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

use PHPUnit\Framework\Assert;
use Teamradhq\DiceRoll\Contracts\Randomiser;

final class TestRandomiser implements Randomiser
{
    /** @var int The number of times the ranomiser was called. */
    private static int $called = 0;

    /** @var int A default value of 1 will be given when values are exhausted. */
    private static int $default = 1;

    /** @var int[] A list of values that will be given in order. */
    private static array $values = [];

    /**
     * Provide values that will be given in order.
     */
    public static function given(int ...$values): self
    {
        self::$values = $values;

        return new self;
    }

    /**
     * Reset the test randomiser to its initial state.
     */
    public static function reset(): void
    {
        self::$called = 0;
        self::$values = [];
    }

    /**
     * Assert that the randomiser was called exactly the number of times.
     */
    public static function assertWasCalled(int $times = 1): void
    {
        Assert::assertSame($times, self::$called);
    }

    /**
     * {@inheritDoc}
     *
     * The call count will be incremented and the next value is provided, or the default once values are exhausted.
     */
    public function value(int $min = 1, int $max = 1): int
    {
        self::$called++;

        return array_shift(self::$values) ?? self::$default;
    }
}
