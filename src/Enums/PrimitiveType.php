<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Enums;

enum PrimitiveType: string
{
    case Bool = 'bool';
    case Float = 'float';
    case Int = 'int';
    case Number = 'number';
    case String = 'string';

    public function matches(mixed $value): bool
    {
        return match ($this) {
            self::Bool => is_bool($value),
            self::Float => is_float($value),
            self::Int => is_int($value),
            self::Number => is_float($value) || is_int($value),
            self::String => is_string($value),
        };
    }
}
