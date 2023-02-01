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
        if (
            $this->right->type === TokenType::Field
            || $this->right->type === TokenType::Builtin
        ) {
            // Like:
            //     field1 > field2
            //     'test' = builtin
            return $this->getSimpleExpression();
        }

        if ($this->left->type === TokenType::Field) {
            // We have a chance to use a json query
            return $this->getFieldExpression();
        }

        // Hopefully a builtin query
        // TODO: Should we reject expressions where the left side is
        //       neither Field nor Builtin?
        return $this->getSimpleExpression();
    }

    private function getFieldExpression(): string
    {
        return match ($this->operator->type) {
            TokenType::Equal => $this->getContainsExpression(false),
            TokenType::Unequal => $this->getContainsExpression(true),
            default => $this->getJsonPathExpression(),
        };
    }

    private function getJsonPathExpression(): string
    {
        return '';
    }

    private function getContainsExpression(bool $isNot): string
    {
        return $isNot ? 'NOT ' : '';
    }

    private function getSimpleExpression(): string
    {
        return $this->getOperand($this->left, $this->context->db, $this->builtins) .
            $this->getOperator($this->operator->type) .
            $this->getOperand($this->right, $this->context->db, $this->builtins);
    }
}
