<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\RollTable;

use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Teamradhq\DiceRoll\Contracts\Modifier;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Enums\PrimitiveType;
use Teamradhq\DiceRoll\RollTable\RollTable;
use Teamradhq\DiceRoll\Tests\Fixture\TestSetupException;
use Teamradhq\DiceRoll\Tests\Unit\RollTable\RollTableTestProvider as Provider;

#[CoversClass(RollTable::class)]
final class RollTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Provider::setUp();
    }

    protected function tearDown(): void
    {
        Provider::tearDown();
        parent::tearDown();
    }

    /**
     * @param array<int, mixed> $items
     *
     * @throws RollException
     * @throws TestSetupException
     */
    #[TestDox('table should roll for $_dataName item.')]
    #[DataProviderExternal(Provider::class, 'primitives')]
    public function test_primitive_rolls(PrimitiveType $primitiveType, array $items): void
    {
        Provider::dice()->expects('result')->andReturn(1, 2, 3);

        [$expectedRoll, $expectedAdvantage, $expectedDisadvantage] = $items;

        $rollTable = Provider::rollTable(3, $primitiveType, $items);

        self::assertSame($expectedRoll, $rollTable->roll());
        self::assertSame($expectedAdvantage, $rollTable->advantage());
        self::assertSame($expectedDisadvantage, $rollTable->disadvantage());
    }

    /**
     * @param array<int, mixed> $args
     * @param array<string, mixed> $expected
     *
     * @throws RollException
     * @throws TestSetupException
     */
    #[TestDox('Created table $_dataName.')]
    #[DataProviderExternal(Provider::class, 'created')]
    public function test_a_table_can_be_created(array $args, array $expected): void
    {
        /** @phpstan-ignore argument.type (Incorrect args will cause test failure.) */
        $rollTable = Provider::rollTable(...$args);

        self::assertSame($expected['size'], $rollTable->size());
        self::assertSame($expected['type'], $rollTable->type());
        self::assertSame($expected['list'], $rollTable->list());
        self::assertSame($expected['isFull'], $rollTable->isFull());
    }

    /**
     * @param (MockInterface&Modifier)|null $mock
     * @throws RollException
     * @throws TestSetupException
     */
    #[TestDox('Roll with $_dataName modifier selects the correct item.')]
    #[DataProviderExternal(Provider::class, 'modifiers')]
    public function test_roll_modifier_is_applied(?MockInterface $mock, int $expected): void
    {
        Provider::dice()->expects('result')->andReturn(2);

        self::assertSame($expected, Provider::rollTable()->roll($mock));
    }

    /**
     * @param callable(RollTable<mixed>):void $shouldThrow
     *
     * @throws RollException
     * @throws TestSetupException
     */
    #[TestDox('Table cannot $_dataName')]
    #[DataProviderExternal(Provider::class, 'errors')]
    public function test_table_handles_errors(callable $shouldThrow, string $expected): void
    {
        $rollTable = Provider::rollTable(4);

        $this->expectException(RollException::class);
        $this->expectExceptionMessageIsOrContains($expected);

        $shouldThrow($rollTable);
    }
}
