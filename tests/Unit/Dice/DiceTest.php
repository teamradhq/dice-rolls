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
     * @throws RollException
     */
    public function test_advantage_is_rolled(): void
    {
        Dice::setUp(TestRandomiser::given(1, 2));

        self::assertSame(2, Dice::make()->advantage()->result());
        TestRandomiser::assertWasCalled(2);
    }

    /**
     * @throws RollException
     */
    public function test_disadvantage_is_rolled(): void
    {
        Dice::setUp(TestRandomiser::given(1, 2));

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
}
