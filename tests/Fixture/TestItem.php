<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

final readonly class TestItem
{
    public function __construct(
        public string $name,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
