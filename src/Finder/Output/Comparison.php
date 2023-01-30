<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Finder\Input\Token;
use Conia\Quma\Database;

readonly final class Comparison extends Expression implements Output
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public Token $right,
        private array $builitns,
        private Database $db
    ) {
    }

    public function get(): string
    {
        return '';
    }
}
