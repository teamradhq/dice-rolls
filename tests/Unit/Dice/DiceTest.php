<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\Dice;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Tests\Fixture\TestRandomiser;

#[CoversClass(Dice::class)]
final class DiceTest extends TestCase
{
    public static function invalidResultProvider(): Generator
    {
        yield 'negative' => [-2];

        yield 'zero' => [0];

        yield 'exceeds max' => [7];
    }

    /**
     * @throws ReflectionException
     */
    #[TestDox('Invalid result should throw an exception.')]
    #[DataProvider('invalidResultProvider')]
    public function test_invalid_result(int $result): void
    {
        $this->expectExceptionMessage('Out of range: must be between 1 and 6.');
        $this->expectException(RollException::class);

        $reflectionClass = new ReflectionClass(Dice::class);
        $constructor = $reflectionClass->getConstructor();
        $constructor?->invokeArgs($reflectionClass->newInstanceWithoutConstructor(), [
            'result' => $result,
        ]);
    }

    /**
     * @throws RollException
     */
    #[TestDox('Roll should always be within range.')]
    public function test_roll_is_always_in_range(): void
    {
        $results = [];

        for ($i = 0; $i < 100; $i++) {
            $results[] = Dice::make()->roll()->result;
        }

        $unique = array_unique($results);
        sort($unique);

        self::assertCount(100, $results);
        self::assertSame([1, 2, 3, 4, 5, 6], $unique);
    }

    /**
     * @throws RollException
     */
    public function test_advantage_is_rolled(): void
    {

        Dice::setRandomiser(TestRandomiser::given(1, 2));

        self::assertSame(2, Dice::make()->advantage()->result);
        TestRandomiser::assertWasCalled(2);

        Dice::setRandomiser(null);
    }
}
