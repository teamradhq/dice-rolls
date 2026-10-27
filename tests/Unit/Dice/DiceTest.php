<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\Dice;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use ReflectionClass;
use ReflectionException;
use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Tests\Fixture\TestRandomiser;

#[CoversClass(Dice::class)]
final class DiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Dice::setUp(null);
        TestRandomiser::reset();

        parent::tearDown();
    }

    public static function invalidResultProvider(): Generator
    {
        yield 'negative' => [-2];

        yield 'zero' => [0];

        yield 'exceeds max' => [7];
    }

    public static function timesProvider(): Generator
    {
        yield 'first' => [[2, 1]];

        yield 'second' => [[1, 2]];
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
        $dice = Dice::make();

        for ($i = 0; $i < 100; $i++) {
            $results[] = $dice->roll()->result();
        }

        $unique = array_unique($results);
        sort($unique);

        self::assertSame(6, $dice->sides());
        self::assertCount(100, $results);
        self::assertSame([1, 2, 3, 4, 5, 6], $unique);
    }

    /**
     * @param int[] $values
     *
     * @throws RollException
     */
    #[TestDox('Advantage gives the $_dataName item with the higher value.')]
    #[DataProvider('timesProvider')]
    public function test_advantage_is_rolled(array $values): void
    {
        Dice::setUp(TestRandomiser::given(...$values));

        self::assertSame(2, Dice::make()->advantage()->result());
        TestRandomiser::assertWasCalled(2);
    }

    /**
     * @param int[] $values
     *
     * @throws RollException
     */
    #[TestDox('Disadvantage does not give the $_dataName item with the higher value.')]
    #[DataProvider('timesProvider')]
    public function test_disadvantage_is_rolled(array $values): void
    {
        Dice::setUp(TestRandomiser::given(...$values));

        self::assertSame(1, Dice::make()->disadvantage()->result());
        TestRandomiser::assertWasCalled(2);
    }

    /**
     * @throws RollException
     */
    public function test_rolled_dice_is_always_unique(): void
    {
        $dice = Dice::make();
        $rolled = $dice->roll();

        self::assertNotSame($rolled, $dice);
        self::assertFalse($dice->isRolled());
        self::assertTrue($rolled->isRolled());
        self::assertSame(-1, $dice->result());
        self::assertNotSame(-1, $rolled->result());
    }

    /**
     * @throws RollException
     */
    public function test_random_error_is_reported(): void
    {

        Dice::setUp(TestRandomiser::throws(new RandomException('Test Error', 123)));

        $this->expectExceptionMessageIsOrContains('Could not roll the dice.');
        $this->expectExceptionCode(123);
        $this->expectException(RollException::class);

        Dice::make()->roll();
    }

    /**
     * @throws RollException
     */
    public function test_multiple_rolls_is_at_least_one(): void
    {
        $this->expectExceptionMessageIsOrContains('Dice must be rolled at least once.');
        $this->expectException(RollException::class);

        Dice::make()->times(0);
    }
}
