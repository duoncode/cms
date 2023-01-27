<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

enum TokenGroup
{
    // Symbols used to group boolean expressions: ( )
    case GroupSymbol;

    // Operators used in boolean expresssions: & |
    case BooleanOperator;

    // Fields and values
    case Operand;

    // Compare operators: = != !~ < > =< => ...
    case Operator;
}
