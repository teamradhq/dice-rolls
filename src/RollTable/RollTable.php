<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\RollTable;

use Teamradhq\DiceRoll\Contracts\Rollable;
use Teamradhq\DiceRoll\Contracts\Table;
use Teamradhq\DiceRoll\Dice\Dice;
use Teamradhq\DiceRoll\Dice\RollException;
use Teamradhq\DiceRoll\Enums\PrimitiveType;

/**
 * @template TableItem
 *
 * @implements Table<TableItem>
 */
abstract class RollTable implements Table
{
    /** @var TableItem[] */
    protected array $items = [];

    protected Rollable $dice;

    /**
     * @param class-string|PrimitiveType $type
     *
     * @throws RollException
     */
    public function __construct(
        protected int $size,
        protected string|PrimitiveType $type,
        ?Rollable $rollable = null,
    ) {
        if ($size < 2) {
            throw new RollException(sprintf(
                'Table size should be greater than 2: %d given.',
                $size
            ));
        }

        $this->dice = $rollable ?? Dice::make($size);
    }

    /**
     * @throws RollException
     */
    public function roll(): mixed
    {
        return $this->rollItem();
    }

    /**
     * @throws RollException
     */
    public function advantage(): mixed
    {
        return $this->rollItem(true);
    }

    /**
     * @throws RollException
     */
    public function disadvantage(): mixed
    {
        return $this->rollItem(false);
    }

    public function list(): array
    {
        /** @phpstan-ignore argument.type (Need to improve this implementation but good for now.) */
        return array_values(array_unique($this->items));
    }

    /**
     * @throws RollException
     */
    public function add(mixed $item, int $slots = 1): void
    {
        if ($slots < 1) {
            throw new RollException(sprintf(
                'Item slots must be 1 or more. %s given.',
                $slots
            ));
        }

        while (--$slots >= 0) {
            $this->addItem($item);
        }
    }

    public function size(): int
    {
        return $this->size;
    }

    public function type(): string
    {
        if (is_string($type = $this->type)) {
            /** @var class-string $type */
            return $type;
        }

        return $this->type->value;
    }

    /**
     * The table is full when the number of items matches its size.
     */
    public function isFull(): bool
    {
        return count($this->items) >= $this->size();
    }

    /**
     * @throws RollException
     */
    private function addItem(mixed $item): void
    {
        if (!$this->isValid($item)) {
            throw new RollException(sprintf(
                'Provided item must be of type %s. %s given.',
                $this->type(),
                gettype($item)
            ));
        }

        if ($this->isFull()) {
            throw new RollException('Cannot add items to a full table.');
        }

        /** @var TableItem $item */
        $this->items[count($this->items) + 1] = $item;
    }

    /**
     * @phpstan-assert-if-true TableItem $item
     */
    private function isValid(mixed $item): bool
    {
        if (is_string($this->type)) {
            return $item instanceof $this->type;
        }

        return match ($this->type) {
            PrimitiveType::Bool => is_bool($item),
            PrimitiveType::Float => is_float($item),
            PrimitiveType::Int => is_int($item),
            PrimitiveType::Number => is_float($item) || is_int($item),
            PrimitiveType::String => is_string($item),
        };
    }

    /**
     * @return TableItem
     * @throws RollException
     */
    private function rollItem(?bool $isAdvantage = null): mixed
    {
        if (!$this->isFull()) {
            throw new RollException(sprintf(
                'Cannot roll when table is not full. '
                . 'Table size is %d, but only %d slots filled.',
                $this->size(),
                count($this->items),
            ));
        }

        $result = match ($isAdvantage) {
            true => $this->dice->advantage(),
            false => $this->dice->disadvantage(),
            null => $this->dice->roll(),
        };

        $item = $this->items[$result->result()] ?? null;

        if (is_null($item)) {
            throw new RollException(sprintf(
                'Item not found at slot %d.',
                $result->result()
            ));
        }

        /** @var TableItem $item */
        return $item;
    }
}
