<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Contracts;

/**
 * @template TableItem
 */
interface Table
{
    /**
     * Roll to get a random item from the table.
     *
     * @return TableItem
     */
    public function roll(?Modifier $modifier = null): mixed;

    /**
     * Roll to get a random item from the table with advantage.
     *
     * @return TableItem
     */
    public function advantage(?Modifier $modifier = null): mixed;

    /**
     * Roll to get a random item from the table with disadvantage.
     *
     * @return TableItem
     */
    public function disadvantage(?Modifier $modifier = null): mixed;

    /**
     * Get a list of all items in the table.
     *
     * @return TableItem[]
     */
    public function list(): array;

    /**
     * Add an item to the table.
     *
     * @param TableItem $item
     */
    public function add(mixed $item, int $slots = 1): void;

    /**
     * Get the size of the table.
     */
    public function size(): int;

    /**
     * Get the table's item type.
     */
    public function type(): string;
}
