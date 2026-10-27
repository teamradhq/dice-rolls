<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

use PHPUnit\Framework\Assert;
use Teamradhq\DiceRoll\Contracts\Randomiser;
use Throwable;

final class TestRandomiser implements Randomiser
{
    /** @var int The number of times the ranomiser was called. */
    private static int $called = 0;

    /** @var int A default value of 1 will be given when values are exhausted. */
    private static int $default = 1;

    /** @var (int|Throwable)[] A list of values that will be given in order. */
    private static array $values = [];

    /**
     * Provide values that will be given in order, including all previously added values.
     */
    public static function given(int ...$values): self
    {
        foreach ($values as $value) {
            self::$values[] = $value;
        }

        return new self;
    }

    /**
     * Provide an exception to throw when a random value is requested. This is added to the value
     * list, so you can add n-1 items followed by an exception to throw on the nth call.
     */
    public static function throws(Throwable $throwable): self
    {
        self::$values[] = $throwable;

        return new self;
    }

    /**
     * Reset the test randomiser to its initial state.
     */
    public static function reset(): void
    {
        self::$called = 0;
        self::$default = 1;
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
     * The call count is incremented and the next or default value is provided. When the next
     * value is an exception this will be thrown.
     *
     * @throws Throwable
     */
    public function value(int $min = 1, int $max = 1): int
    {
        self::$called++;

        $next = array_shift(self::$values) ?? self::$default;

        if ($next instanceof Throwable) {
            throw $next;
        }

        return $next;
    }
}
