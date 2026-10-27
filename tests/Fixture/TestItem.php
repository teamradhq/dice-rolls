<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

use Stringable;

final readonly class TestItem implements Stringable
{
    public function __construct(
        public string $name,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
