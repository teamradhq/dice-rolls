<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\Dice;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teamradhq\DiceRoll\Dice\Compare;
use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Tests\Fixture\TestRandomiser;

#[CoversClass(Compare::class)]
final class CompareTest extends TestCase
{
    /**
     * @return array{0: Dice, 1: Dice}
     *
     * @throws RollException
     */
    public static function dice(int $a = 1, int $b = 2): array
    {
        TestRandomiser::given($a, $b);

        $dice = Dice::make();
        $dice::setUp(new TestRandomiser);

        return [
            $dice->roll(),
            $dice->roll(),
        ];
    }

    /**
     * @return Generator<array{0: int, 1: int, 2: bool, 3: callable(Compare, Dice, Dice): bool }>
     */
    public static function provideComparison(): Generator
    {
        yield 'is greater than' => [2, 1, true, $gt = static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->gt($a, $b)];

        yield 'is not greater than' => [1, 1, false, $gt];

        yield 'is greater than or equal' => [1, 1, true, $gte = static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->gte($a, $b)];

        yield 'is not greater than or equal' => [1, 2, false, $gte];

        yield 'is less than' => [1, 2, true, $lt = static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->lt($a, $b)];

        yield 'is not less than' => [1, 1, false, $lt];

        yield 'is less than or equal' => [1, 1, true, $lte = static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->lte($a, $b)];

        yield 'is not less than or equal' => [2, 1, false, $lte];

        yield 'is equal' => [1, 1, true, static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->lte($a, $b)];

        yield 'is not equal' => [1, 2, true, static fn (
            Compare $compare,
            Dice $a,
            Dice $b
        ): bool => $compare->neq($a, $b)];
    }

    /**
     * @param callable(Compare, Dice, Dice): bool $test
     * @throws RollException
     */
    #[DataProvider('provideComparison')]
    public function test_comparison(int $a, int $b, bool $expected, callable $test): void
    {
        [$diceA, $diceB] = self::dice($a, $b);

        self::assertSame($expected, $test(new Compare, $diceA, $diceB));

        TestRandomiser::reset();
    }
}
