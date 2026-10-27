<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Unit\Enums;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Teamradhq\DiceRoll\Enums\PrimitiveType;

#[CoversClass(PrimitiveType::class)]
final class PrimitiveTypeTest extends TestCase
{
    public static function caseProvider(): Generator
    {
        $values = [
            'null' => null,
            'true' => true,
            'false' => false,
            'int' => 1,
            'float' => 2.2,
            'string' => 'words',
        ];

        $label = static fn (string $case, bool $match, string $type): string => sprintf(
            '%s %s %s',
            $case,
            $match ? 'matches' : 'does not match',
            $type
        );

        foreach ($values as $case => $value) {
            $expected = is_string($value);

            yield $label($case, $expected, 'String') => [
                $value,
                PrimitiveType::String,
                $expected,
            ];

            $expected = is_bool($value);

            yield $label($case, $expected, 'Bool') => [
                $value,
                PrimitiveType::Bool,
                $expected,
            ];

            $expected = is_float($value);

            yield $label($case, $expected, 'Float') => [
                $value,
                PrimitiveType::Float,
                $expected,
            ];

            $expected = is_int($value);

            yield $label($case, $expected, 'Int') => [
                $value,
                PrimitiveType::Int,
                $expected,
            ];

            $expected = is_float($value) || is_int($value);

            yield $label($case, $expected, 'Number') => [
                $value,
                PrimitiveType::Number,
                $expected,
            ];
        }
    }

    #[TestDox('Value of $_dataName.')]
    #[DataProvider('caseProvider')]
    public function test_value_matches_type(mixed $value, PrimitiveType $primitiveType, bool $expected): void
    {
        self::assertSame($expected, $primitiveType->matches($value));
    }
}
