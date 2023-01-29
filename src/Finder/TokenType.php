<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

enum TokenType
{
    // Single character tokens
    case LeftParen;
    case RightParen;

    // Operators
    case Equal;
    case Greater;
    case GreaterEqual;
    case Less;
    case LessEqual;
    case Like;
    case Unequal;
    case Unlike;

    // Boolean Operators
    case And;
    case Or;

    // Operands
    case Boolean;
    case Builtin;
    case Exists;
    case Path;
    case Field;
    case Keyword;
    case Null;
    case Number;
    case String;
}
