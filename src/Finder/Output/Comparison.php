<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Finder\Context;
use Conia\Core\Finder\Input\Token;
use Conia\Core\Finder\Input\TokenType;

readonly final class Comparison extends Expression implements Output
{
    public function __construct(
        public Token $left,
        public Token $operator,
        public Token $right,
        private Context $context,
        private array $builtins,
    ) {
    }

    public function get(): string
    {
        if ($this->left->type === TokenType::Field) {
            return $this->getFieldExpression();
        }

        return $this->getOperand($this->left, $this->context->db, $this->builtins) .
            $this->getOperator($this->operator->type) .
            $this->getOperand($this->right, $this->context->db, $this->builtins);
    }

    private function getFieldExpression(): string
    {
        $parts = explode('.', $this->left->lexeme);

        if ($this->operator->type === TokenType::Equal) {
            if (count($parts) > 1) {
            }
        }

        return '';
    }
}
