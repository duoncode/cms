<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Input;

enum TokenGroup
{
    // Prenthesis used to group boolean expressions: ( )
    case LeftParen;
    case RightParen;

    // Operators used in boolean expresssions: & |
    case BooleanOperator;

    // Fields and values
    case Operand;

    // Compare operators: = != !~ < > =< => ...
    case Operator;
}
