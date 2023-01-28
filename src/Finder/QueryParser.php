<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class QueryParser
{
    /** @psalm-type list<Token> */
    private array $tokens = [];

    private int $operandsAndOperators;
    private int $pos;
    private int $length;
    private bool $readyForCondition = true;

    public function __construct(private readonly string $query)
    {
        $this->tokens = (new QueryLexer($query))->tokens();
        $this->length = count($this->tokens);
    }

    /**
     * Returns a stream of tokens which can be translated to a
     * valid SQL WHERE expression.
     *
     * Does not transform the token stream. It simply checks if it is valid
     * and returns it as it is.
     *
     * @psalm-return list<Token>
     */
    public function parse(): array
    {
        $parensBalance = 0;
        $this->operandsAndOperators = 0;
        $this->readyForCondition = true;
        $this->pos = 0;

        while ($this->pos < $this->length) {
            $token = $this->tokens[$this->pos];
            $type = $token->type;

            switch ($token->group) {
                case TokenGroup::Operand:
                    $this->validateCondition($token);
                    break;
                case TokenGroup::Operator:
                    // As we consume operators together with operands, it would
                    // be invalid if we would find operators anywhere else.
                    $this->error($token, 'Invalid position for operator.');

                    break;
                case TokenGroup::BooleanOperator:
                    $this->validateBooleanOperator($token);
                    break;
                case TokenGroup::GroupSymbol:
                    $this->validateGroup($type, $token);
                    break;
            }

            if ($type === TokenType::LeftParen) {
                $parensBalance++;
            } elseif ($type === TokenType::RightParen) {
                $parensBalance--;
            }

            if ($parensBalance < 0) {
                throw new ParserException('Parse error. Unbalanced parentheses');
            }
        }

        if ($parensBalance > 0) {
            throw new ParserException('Parse error. Unbalanced parentheses');
        }

        return $this->tokens;
    }

    /**
     * @throws ParserException
     */
    private function validateCondition(Token $token): void
    {
        if (!$this->readyForCondition) {
            $this->error($token, 'Invalid position for a condition.');
        }

        // Consume the whole condition if valid
        if (
            $this->pos + 2 <= $this->length
            && $this->tokens[$this->pos + 1]->group === TokenGroup::Operator
            && $this->tokens[$this->pos + 2]->group === TokenGroup::Operand
        ) {
            $this->readyForCondition = false;
            $this->operandsAndOperators += 3;
            $this->pos += 3;
        } elseif (
            $this->pos + 2 <= $this->length
            && $this->tokens[$this->pos + 1]->group === TokenGroup::Operator
            && $this->tokens[$this->pos + 2]->group === TokenGroup::Operator
        ) {
            $this->error($token, 'Multiple operators. Maybe you used == instead of =.');
        } else {
            $this->error($token, 'Invalid condition.');
        }
    }

    /**
     * @throws ParserException
     */
    private function validateBooleanOperator(Token $token): void
    {
        if ($this->readyForCondition) {
            $this->error(
                $token,
                'Invalid position for boolean operator. ' .
                    'Maybe you used && instead of & or || instead of |'
            );
        }

        if ($this->pos >= $this->length - 1) {
            $this->error($token, 'Boolean operator at the end of the expression.');
        }

        $this->readyForCondition = true;
        $this->pos++;
    }

    /**
     * @throws ParserException
     */
    private function validateGroup(TokenType $type, Token $token): void
    {
        if ($type === TokenType::RightParen) {
            if (
                $this->pos > 0
                && $this->tokens[$this->pos - 1]->type === TokenType::LeftParen
            ) {
                $this->error(
                    $token,
                    'Empty group.'
                );
            }

            $this->readyForCondition = false;
        } else {
            if (!$this->readyForCondition) {
                $this->error($token, 'Invalid position for parenthesis.');
            }
        }

        $this->pos++;
    }

    /**
     * @throws ParserException
     */
    private function error(Token $token, string $msg): never
    {
        $position = $token->position + 1;

        throw new ParserException(
            "Parse error at position {$position}. {$msg}\n" .
                "Query: `{$this->query}`"
        );
    }
}
