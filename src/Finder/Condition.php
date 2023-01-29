<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

readonly class Token
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public int $right,
        public string $lexeme
    ) {
    }

    public function len(): int
    {
        return strlen($this->lexeme);
    }
}
