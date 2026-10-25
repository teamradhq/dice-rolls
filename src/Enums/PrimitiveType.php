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
}
