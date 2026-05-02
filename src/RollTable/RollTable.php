<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\RollTable;

use stdClass;
use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\Rollable;
use Teamradhq\DiceRoll\Dice\RollException;

/**
 * @template TItem of object
 */
final class RollTable
{
    /** @var class-string<TItem> */
    public readonly string $itemClass;

    /** @var array<int, true> */
    private array $availableKeys;

    /** @var array<int, TItem> */
    private array $items = [];

    private readonly Rollable $rollable;

    /**
     * @throws RollException
     * @throws TableException
     */
    public function __construct(
        public readonly int $size,
        string $itemClass,
        ?Rollable $rollable = null
    ) {
        if (!class_exists($itemClass)) {
            throw new TableException(sprintf('Class %s does not exist.', $itemClass));
        }

        /** @var class-string<TItem> $itemClass */
        $this->itemClass = $itemClass;
        $this->availableKeys = array_fill(1, $size, true);
        $this->rollable = $rollable ?? Dice::make($this->size);
    }

    /**
     * @param TItem $item
     *
     * @throws TableException
     */
    public function add(object $item, int $min, ?int $max = null): void
    {
        if (
            is_subclass_of($item, $this->itemClass)
            || ($this->itemClass !== stdClass::class && $item instanceof stdClass)
        ) {
            throw new TableException(
                sprintf('Invalid type: %s. All items must be %s instances', $item::class, $this->itemClass)
            );
        }

        foreach (range($min, $max ?? $min) as $index) {
            if (!isset($this->availableKeys[$index])) {
                throw new TableException(sprintf('Index %d is unavailable', $index));
            }

            unset($this->availableKeys[$index]);
            $this->items[$index] = $item;
        }
    }

    /**
     * @return TItem
     *
     * @throws RollException
     * @throws TableException
     */
    public function roll(): object
    {
        if (!$this->isTableFull()) {
            throw new TableException('Cannot roll when table is not filled.');
        }

        if (!$this->isIndexInRange($index = $this->rollable->roll()->result())) {
            throw new TableException(sprintf('Index %d is out of bounds.', $index));
        }

        return $this->items[$index];
    }

    private function isIndexInRange(int $index): bool
    {
        return $index >= 1 && $index <= $this->size;
    }

    private function isTableFull(): bool
    {
        return $this->availableKeys === [];
    }
}
