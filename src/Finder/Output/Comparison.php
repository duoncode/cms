<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

use Conia\Core\Exception\ParserOutputException;
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
            switch ($this->operator->type) {
                case TokenType::Like:
                case TokenType::Unlike:
                case TokenType::ILike:
                case TokenType::IUnlike:
                    return $this->getSimpleExpression();
                default:
                    return $this->getFieldExpression();
            }
        }

        if ($this->left->type === TokenType::Builtin) {
            return $this->getSimpleExpression();
        }

        if ($this->left->type === TokenType::Path) {
            return $this->getPathExpression();
        }

        throw new ParserOutputException(
            $this->left,
            'Only fields or `path` are allowed on the left side of an expresseion'
        );
    }

    private function getFieldExpression(): string
    {
        [$operator, $jsonOperator, $right] = match ($this->operator->type) {
            TokenType::Equal => ['@@', '==', $this->getRight()],
            TokenType::Regex => ['@?', '?', $this->getRegex(false)],
            TokenType::IRegex => ['@?', '?', $this->getRegex(true)],
            TokenType::NotRegex => ['@?', '?', $this->getRegex(false)],
            TokenType::INotRegex => ['@?', '?', $this->getRegex(true)],
            default => ['@@', $this->operator->lexeme, $this->getRight()],
        };

        $left = $this->getField();

        return sprintf("content %s '$.%s %s %s'", $operator, $left, $jsonOperator, $right);
    }

    private function getRegex(bool $ignoreCase): string
    {
        if (!($this->right->type === TokenType::String)) {
            throw new ParserOutputException(
                $this->right,
                'Only fields or `path` are allowed on the left side of an expresseion'
            );
        }

        $case = $ignoreCase ? ' flag "i"' : '';

        // TODO: quote double quotes, check also in tests
        $pattern = '"' . trim("'", $this->context->db->quote($this->right->lexeme)) . '"';

        return sprintf('(@ like_regex %s%s)', $pattern, $case);
    }

    private function getField(): string
    {
        return '';
    }

    private function getRight(): string
    {
        switch ($this->right->type) {
            case TokenType::String:
                return trim("'", $this->context->db->quote($this->right->lexeme));
            case TokenType::Number:
            case TokenType::Boolean:
            case TokenType::Null:
                return $this->right->lexeme;
            default:
                throw new ParserOutputException(
                    $this->right,
                    'The right hand side in a field expression must be a literal'
                );
        }
    }

    private function getSimpleExpression(): string
    {
        return $this->getOperand($this->left, $this->context->db, $this->builtins) .
            $this->getOperator($this->operator->type) .
            $this->getOperand($this->right, $this->context->db, $this->builtins);
    }

    private function getPathExpression(): string
    {
        return $this->getOperand($this->left, $this->context->db, $this->builtins) .
            $this->getOperator($this->operator->type) .
            $this->getOperand($this->right, $this->context->db, $this->builtins);
    }

    private function getLocales(): Iterator
    {
        foreach ($this->context->config->locales() as $locale) {
            yield $locale->id;
        }
    }
}
