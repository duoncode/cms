<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Finder\Input\Token;

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
