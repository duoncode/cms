<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

readonly class Token
{
    public function __construct(
        public TokenGroup $group,
        public TokenKind $kind,
        public string $lexeme
    )
    {
    }
}
