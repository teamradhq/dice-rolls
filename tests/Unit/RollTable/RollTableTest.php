<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\RollTable;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teamradhq\DiceRoll\Contracts\Rollable;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Enums\PrimitiveType;
use Teamradhq\DiceRoll\RollTable\RollTable;
use Throwable;

#[CoversClass(RollTable::class)]
final class RollTableTest extends TestCase
{
    /** @var MockInterface&Rollable */
    public MockInterface $dice;

    /** @var ?RollTable<mixed> */
    public ?RollTable $rollTable = null;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dice = Mockery::mock(Rollable::class);
    }

    protected function tearDown(): void
    {
        $this->rollTable = null;
        parent::tearDown();
    }

    /**
     * @param class-string|PrimitiveType $type
     * @param array<int, mixed> $items
     *
     * @return RollTable<mixed>
     *
     * @throws RollException
     */
    public function rollTable(
        int $size = 3,
        string|PrimitiveType $type = PrimitiveType::Int,
        array $items = [1, 2, 3]
    ): RollTable {
        $this->rollTable ??= new class ($size, $type, $this->dice) extends RollTable {};

        foreach ($items as $item) {
            $this->rollTable->add($item);
        }

        return $this->rollTable;
    }

    /**
     * @throws RollException
     */
    public function test_rolling(): void
    {
        $this->dice->expects('roll')->andReturn($this->dice);
        $this->dice->expects('result')->andReturn(3);

        $rollTable = $this->rollTable();

        self::assertSame(3, $rollTable->roll());
    }
}
