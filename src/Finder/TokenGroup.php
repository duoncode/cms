<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

enum TokenGroup
{
    case BooleanSymbol;
    case Operand;
    case Operator;
}
