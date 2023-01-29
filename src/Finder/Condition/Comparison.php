<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

readonly class Comparison extends Condition
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public Token $right,
    ) {
    }

    public function print(): string
    {
        return '';
    }
}
