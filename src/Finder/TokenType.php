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
    case Unequal;
    case Greater;
    case GreaterEqual;
    case Less;
    case LessEqual;
    case Like;
    case NotLike;

    // Boolean Operators
    case And;
    case Or;

    // Right hand values: Literals
    case String;
    case Number;
    case Boolean;
    case Null;

    // Left hand values: Fields
    case Field;

    // Right hand values: Identifiers like 'now'
    case Identifier;
}
