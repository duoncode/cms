<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

readonly class Exists implements Condition
{
    public function __construct(private Token $token)
    {
    }

    public function print(): string
    {
        return '';
    }
}
