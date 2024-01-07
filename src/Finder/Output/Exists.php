<?php

declare(strict_types=1);

namespace Conia\Cms\Finder\Output;

use Conia\Cms\Finder\Input\Token;

final readonly class Exists extends Expression implements Output
{
    public function __construct(
        private Token $token,
    ) {
    }

    public function get(): string
    {
        return '';
    }
}
