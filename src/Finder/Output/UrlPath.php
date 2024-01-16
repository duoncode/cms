<?php

declare(strict_types=1);

namespace Conia\Cms\Finder\Output;

use Conia\Cms\Finder\Input\Token;

final readonly class UrlPath extends Expression implements Output
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
