<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Random\RandomException;
use Teamradhq\DiceRoll\RandomInt;

#[CoversClass(RandomInt::class)]
final class RandomIntTest extends TestCase
{
    /**
     * @throws RandomException
     */
    #[TestDox('Random integer should always be within range.')]
    public function test_value_is_always_in_range(): void
    {
        $results = [];
        $randomInt = new RandomInt;

        for ($i = 0; $i < 100; $i++) {
            $results[] = $randomInt->value();
        }

        $unique = array_unique($results);
        sort($unique);

        self::assertCount(100, $results);
        self::assertCount(6, $unique);
        self::assertSame([1, 2, 3, 4, 5, 6], $unique);
    }
}
