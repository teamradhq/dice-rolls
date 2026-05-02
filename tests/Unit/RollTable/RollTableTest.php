<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\RollTable;

use Generator;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teamradhq\DiceRoll\Dice\Rollable;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\RollTable\RollTable;
use Teamradhq\DiceRoll\RollTable\TableException;
use Teamradhq\DiceRoll\Tests\Fixture\TestItem;

#[CoversClass(RollTable::class)]
final class RollTableTest extends TestCase
{
    public static function invalidResultProvider(): Generator
    {
        yield 'negative' => [-2, "Index -2 is out of bounds."];

        yield 'zero' => [0, "Index 0 is out of bounds."];

        yield 'positive' => [3, "Index 3 is out of bounds."];
    }

    /**
     * @throws TableException
     * @throws RollException
     */
    public function test_invalid_roll_table_is_not_created(): void
    {
        $this->expectExceptionMessage('Class invalid does not exist.');
        $this->expectException(TableException::class);

        new RollTable(6, 'invalid');
    }

    /**
     * @throws RollException
     * @throws TableException
     */
    public function test_valid_roll_table_is_created(): void
    {
        /** @var RollTable<TestItem> $rollTable */
        $rollTable = new RollTable(2, TestItem::class);
        $rollTable->add(new TestItem('a'), 1);
        $rollTable->add(new TestItem('b'), 2);

        self::assertContains($rollTable->roll()->name, ['a', 'b']);
    }

    /**
     * @throws RollException
     * @throws TableException
     */
    #[DataProvider('invalidResultProvider')]
    public function test_invalid_table_index_is_forbidden(int $result, string $message): void
    {
        $mock = Mockery::mock(Rollable::class);
        $mock->allows('roll')->andReturn($mock);
        $mock->allows('result')->andReturn($result);

        /** @var RollTable<TestItem> $rollTable */
        $rollTable = new RollTable(2, TestItem::class, $mock);
        $rollTable->add(new TestItem('a'), 1);
        $rollTable->add(new TestItem('b'), 2);

        $this->expectException(TableException::class);
        $this->expectExceptionMessage($message);

        $rollTable->roll();
    }

    /**
     * @throws RollException
     * @throws TableException
     */
    public function test_cannot_roll_if_table_is_not_full(): void
    {
        /** @var RollTable<TestItem> $rollTable */
        $rollTable = new RollTable(2, TestItem::class);
        $rollTable->add(new TestItem('a'), 1);

        $this->expectException(TableException::class);
        $this->expectExceptionMessage('Cannot roll when table is not filled.');

        $rollTable->roll();
    }

    /**
     * @throws RollException
     * @throws TableException
     */
    public function test_item_can_span_multiple_rows(): void
    {
        $mock = Mockery::mock(Rollable::class);
        $mock->allows('roll')->andReturn($mock);
        $mock->allows('result')->andReturn(1, 2, 3);

        /** @var RollTable<TestItem> $rollTable */
        $rollTable = new RollTable(3, TestItem::class, $mock);
        $rollTable->add(new TestItem('a'), 1);
        $rollTable->add(new TestItem('b'), 2, 3);

        self::assertSame('a', $rollTable->roll()->name);
        self::assertSame('b', $rollTable->roll()->name);
        self::assertSame('b', $rollTable->roll()->name);
    }
}
