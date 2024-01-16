<?php

declare(strict_types=1);

namespace Conia\Cms\Finder\Output;

use Conia\Cms\Finder\Input\Token;

class LeftParen implements Output
{
    public function __construct(
        public Token $token
    ) {
    }

    public function get(): string
    {
        return '(';
    }
}
