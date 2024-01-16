<?php

declare(strict_types=1);

namespace Conia\Cms\Finder\Output;

use Conia\Cms\Exception\ParserException;
use Conia\Cms\Finder\Input\Token;
use Conia\Cms\Finder\Input\TokenType;

class Operator implements Output
{
    public function __construct(
        public Token $token
    ) {
    }

    public function get(): string
    {
        return match ($this->token->type) {
            TokenType::And => ' AND ',
            TokenType::Or => ' OR ',
            default => throw new ParserException('Invalid boolean operator'),
        };
    }
}
