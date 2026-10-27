<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll;

use Teamradhq\DiceRoll\Contracts\Modifier;
use Teamradhq\DiceRoll\Contracts\Rollable;
use Teamradhq\DiceRoll\Contracts\Table;

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
                'Table rows should be greater than 2: %d given.',
                $size
            ));
        }

        $this->dice = $rollable ?? Dice::make($size);
    }

    /**
     * @throws RollException
     */
    public function roll(?Modifier $modifier = null): mixed
    {
        return $this->rollItem($modifier);
    }

    /**
     * @throws RollException
     */
    public function advantage(?Modifier $modifier = null): mixed
    {
        return $this->rollItem($modifier, true);
    }

    /**
     * @throws RollException
     */
    public function disadvantage(?Modifier $modifier = null): mixed
    {
        return $this->rollItem($modifier, false);
    }

    public function list(): array
    {
        /** @phpstan-ignore argument.type (Need to improve this implementation but good for now.) */
        return array_values(array_unique($this->items));
    }

    /**
     * @throws RollException
     */
    public function get(int $row): mixed
    {
        $this->guardRow($row);

        return $this->items[$row];
    }

    /**
     * @throws RollException
     */
    public function add(mixed $item, int $rows = 1): void
    {
        if ($rows < 1) {
            throw new RollException(sprintf(
                'Item rows must be 1 or more. %s given.',
                $rows
            ));
        }

        while (--$rows >= 0) {
            $this->addItem($item);
        }
    }

    public function rows(): int
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
        return count($this->items) >= $this->rows();
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
     * Roll to get an item, applying the result modifier when provided.
     *
     * @return TableItem
     *
     * @throws RollException
     */
    private function rollItem(?Modifier $modifier = null, ?bool $isAdvantage = null): mixed
    {
        $index = $this->getResult($isAdvantage);

        if ($modifier instanceof Modifier === false) {
            return $this->getItem($index);
        }

        $index += $modifier->value();

        return $this->getItem(match (true) {
            $index < 1 => 1,
            $index > $this->rows() => $this->rows(),
            default => $index,
        });
    }

    /**
     * Get the result of a roll with or without advantage or disadvantage.
     *
     * @throws RollException When the table is not full, or there is an issue rolling the dice.
     */
    private function getResult(?bool $isAdvantage = null): int
    {
        if (!$this->isFull()) {
            throw new RollException(sprintf(
                'Cannot roll when table is not full. '
                . 'Table has %d rows, but only %d filled.',
                $this->rows(),
                count($this->items),
            ));
        }

        $result = match ($isAdvantage) {
            true => $this->dice->advantage(),
            false => $this->dice->disadvantage(),
            null => $this->dice->roll(),
        };

        return $result->result();
    }

    /**
     * Get the table item from the provided table row.
     *
     * @return TableItem
     *
     * @throws RollException When an item cannot be found.
     */
    private function getItem(int $row): mixed
    {
        $this->guardRow($row);

        return $this->items[$row];
    }

    /**
     * @phpstan-assert-if-true TableItem $item
     */
    private function isValid(mixed $item): bool
    {
        if (is_string($this->type)) {
            return $item instanceof $this->type;
        }

        return $this->type->matches($item);
    }

    /**
     * @throws RollException
     */
    private function guardRow(int $row): void
    {
        if (!array_key_exists($row, $this->items)) {
            throw new RollException(sprintf(
                'Item not found at row %d.',
                $row
            ));
        }
    }
}
