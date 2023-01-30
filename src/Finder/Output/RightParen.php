<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Finder\Input\Token;

class RightParen implements Output
{
    public function __construct(
        public Token $token
    ) {
    }

    public function get(): string
    {
        return ')';
    }
}
