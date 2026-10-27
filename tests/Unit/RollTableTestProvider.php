<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit;

use Generator;
use Mockery\MockInterface;
use Teamradhq\DiceRoll\Contracts\Modifier;
use Teamradhq\DiceRoll\Contracts\Rollable;
use Teamradhq\DiceRoll\PrimitiveType;
use Teamradhq\DiceRoll\RollException;
use Teamradhq\DiceRoll\RollTable;
use Teamradhq\DiceRoll\Tests\Fixture\Mockery;
use Teamradhq\DiceRoll\Tests\Fixture\TestItem;
use Teamradhq\DiceRoll\Tests\Fixture\TestSetupException;

final class RollTableTestProvider
{
    /** @var (MockInterface&Rollable)|null */
    public static ?MockInterface $dice = null;

    /** @var ?RollTable<mixed> */
    public static ?RollTable $rollTable = null;

    public static function setUp(): void
    {
        self::tearDown();
    }

    public static function tearDown(): void
    {
        self::$dice = null;
        self::$rollTable = null;
    }

    /**
     * @param class-string|PrimitiveType $type
     * @param array<int, mixed> $items
     * @return RollTable<mixed>
     *
     * @throws RollException
     * @throws TestSetupException
     */
    public static function rollTable(
        int $size = 3,
        string|PrimitiveType $type = PrimitiveType::Int,
        array $items = [1, 2, 3]
    ): RollTable {
        self::$rollTable ??= new class ($size, $type, self::dice()) extends RollTable {};

        foreach ($items as $item) {
            self::$rollTable->add($item);
        }

        return self::$rollTable;
    }

    /**
     * Provides the mock dice used by the roll table, allowing for roll, advantage,
     * and disadvantage calls.
     *
     * @return MockInterface&Rollable
     *
     * @throws TestSetupException
     */
    public static function dice(): MockInterface
    {
        if (self::$dice instanceof MockInterface === false) {
            self::$dice = Mockery::mock(Rollable::class);
            self::$dice->allows('roll')->andReturn(self::$dice);
            self::$dice->expects('advantage')->andReturn(self::$dice);
            self::$dice->expects('disadvantage')->andReturn(self::$dice);
        }

        return self::$dice;
    }

    /**
     * @return Generator<string, array{0: PrimitiveType, 1: bool[]|float[]|(float|int)[]|int[]|string[]}>
     */
    public static function primitives(): Generator
    {
        yield 'string' => [
            PrimitiveType::String,
            ['a', 'b', 'c'],
        ];

        yield 'bool' => [
            PrimitiveType::Bool,
            [true, true, false],
        ];

        yield 'float' => [
            PrimitiveType::Float,
            [2.2, 4.4, 6.6],
        ];

        yield 'int' => [
            PrimitiveType::Int,
            [2, 4, 6],
        ];

        yield 'number' => [
            PrimitiveType::Number,
            [2, 4.4, 6],
        ];
    }

    /**
     * @return Generator<string, array{0: array<int, mixed>, 1: array<string, mixed>}>
     */
    public static function created(): Generator
    {
        yield 'is full' => [
            [3, PrimitiveType::Int],
            $defaults = ['size' => 3, 'type' => 'int', 'list' => [1, 2, 3], 'isFull' => true],
        ];

        yield 'is partially filled' => [
            [3, PrimitiveType::Int, [1, 2]],
            [...$defaults, 'list' => [1, 2], 'isFull' => false],
        ];

        yield 'has type of object' => [
            [2, $class = TestItem::class, $list = [new TestItem('1'), new TestItem('2')]],
            [...$defaults, 'size' => 2, 'type' => $class, 'list' => $list],
        ];

        yield 'has unique item list' => [
            [3, PrimitiveType::Int, [1, 1, 1]],
            [...$defaults, 'list' => [1]],
        ];
    }

    /**
     * @return Generator<string, array{1: (MockInterface&Modifier)|null, 1: int}>
     * @throws TestSetupException
     */
    public static function modifiers(): Generator
    {
        yield 'no' => [null, 2];

        $cases = [
            'positive' => [1, 3],
            'negative' => [-1, 1],
            'positive out of bounds' => [10, 3],
            'negative out of bounds' => [-10, 1],
        ];

        foreach ($cases as $label => [$value, $expected]) {
            $modifier = Mockery::mock(Modifier::class);
            $modifier->expects('value')->andReturn($value);

            yield $label => [$modifier, $expected];
        }
    }

    /**
     * @return Generator<string, array{0: callable(RollTable<mixed>): void, 1: string }>
     */
    public static function errors(): Generator
    {
        yield 'add item with non-positive rows' => [
            function (RollTable $rollTable): void {
                $rollTable->add(1, 0);
            },
            'Item rows must be 1 or more. 0 given.',
        ];

        yield 'add invalid item' => [
            function (RollTable $rollTable): void {
                $rollTable->add(false);
            },
            'Provided item must be of type int. boolean given.',
        ];

        yield 'add to full table' => [
            function (RollTable $rollTable): void {
                $rollTable->add(1, 100);
            },
            'Cannot add items to a full table.',
        ];

        yield 'roll when table is not full' => [
            function (RollTable $rollTable): void {
                $rollTable->roll();
            },
            'Cannot roll when table is not full. Table has 4 rows, but only 3 filled.',
        ];

        yield 'cannot get item when row is not found' => [
            function (RollTable $rollTable): void {
                $rollTable->get(1000);
            },
            'Item not found at row 1000.',
        ];

        yield 'cannot get item when row is less than one' => [
            function (RollTable $rollTable): void {
                $rollTable->get(0);
            },
            'Item not found at row 0.',
        ];

        yield 'finish roll when item is not found' => [
            function (RollTable $rollTable): void {
                self::dice()->expects('result')->andReturn(1000);
                $rollTable->add(1);
                $rollTable->roll();
            },
            'Item not found at row 1000.',
        ];

        yield 'create a size lower than 2' => [
            function (RollTable $rollTable): void {
                new class (1, PrimitiveType::Int) extends RollTable {};
            },
            'Table rows should be greater than 2: 1 given.',
        ];
    }
}
