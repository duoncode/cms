<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Exception\ParserException;
use Conia\Core\Finder\Input\Token;
use Conia\Core\Finder\Input\TokenType;

class Operator implements Output
{
    public function __construct(
        public Token $token
    ) {
    }

    public function get(): string
    {
        return match ($this->token->type) {
            TokenType::And => 'AND',
            TokenType::Or => 'OR',
            default => throw new ParserException('Invalid boolean operator'),
        };
    }
}
