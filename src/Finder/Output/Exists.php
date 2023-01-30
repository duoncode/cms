<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

readonly class Exists extends Condition implements Output
{
    public function __construct(private Token $token)
    {
    }

    public function get(): string
    {
        return '';
    }
}
