<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Finder\Input\Token;

readonly final class UrlPath extends Expression implements Output
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public Token $right,
    ) {
    }

    public function get(): string
    {
        return '';
    }
}
