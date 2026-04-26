<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

use PHPUnit\Framework\Assert;
use Teamradhq\DiceRoll\Dice\Randomiser;

final class TestRandomiser implements Randomiser
{
    private static int $called = 0;

    /** @var int[] */
    private static array $values = [];

    public static function given(int ...$values): self
    {
        self::$values = $values;

        return new self;
    }

    public static function reset(): void
    {
        self::$called = 0;
        self::$values = [];
    }

    public static function assertWasCalled(int $times): void
    {
        Assert::assertSame($times, self::$called);
    }

    public function value(int $min = 1, int $max = 1): int
    {
        self::$called++;

        return array_shift(self::$values) ?? 1;
    }
}
