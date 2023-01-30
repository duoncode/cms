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
        private Database $db,
        private array $builtins,
    ) {
    }

    public function get(): string
    {
        return $this->getOperand($this->left, $this->db, $this->builtins) .
            $this->getOperator($this->operator->type) .
            $this->getOperand($this->right, $this->db, $this->builtins);
    }
}
