<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Finder\Input\Token;

readonly final class Comparison extends Condition implements Output
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public Token $right,
        private array $builtins,
    ) {
    }

    public function get(): string
    {
        return '';
    }
}
