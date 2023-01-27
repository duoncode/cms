<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class QueryParser
{
    /** @psalm-type list<Token> */
    private array $tokens = [];

    private int $parensBalance = 0;
    private int $operandsAndOperators = 0;
    private int $pos = 0;
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
    public function tokens(): array
    {
        $this->parensBalance = 0;
        $this->operandsAndOperators = 0;
        $this->readyForCondition = true;
        $this->pos = 0;

        $tokens = $this->tokens;

        while ($this->pos < $this->length) {
            $token = $tokens[$this->pos];
            $type = $token->type;

            switch ($token->group) {
                case TokenGroup::Operand:
                    if (!$this->readyForCondition) {
                        $this->error($token, 'Invalid position for a condition.');
                    }

                    // Consume the whole condition if valid
                    if (
                        $this->pos + 2 <= $this->length
                        && $tokens[$this->pos + 1]->group === TokenGroup::Operator
                        && $tokens[$this->pos + 2]->group === TokenGroup::Operand
                    ) {
                        $this->operandsAndOperators += 3;
                        $this->pos += 3;
                    } else {
                        $this->error($token, 'Invalid condition.');
                    }

                    $this->readyForCondition = false;

                    break;
                case TokenGroup::Operator:
                    // As we consume operators together with operands, it would
                    // be invalid if we would find operators anywhere else.
                    $this->error($token, 'Invalid position for operator.');

                    break;
                case TokenGroup::BooleanOperator:
                    if ($this->readyForCondition) {
                        $this->error(
                            $token,
                            'Invalid position for boolean operator. ' .
                                'Maybe you used && instead of & or || instead of |'
                        );
                    }

                    $this->readyForCondition = true;
                    $this->pos++;

                    break;
                case TokenGroup::GroupSymbol:
                    if ($type === TokenType::RightParen) {
                        if (
                            $this->pos > 0
                            && $tokens[$this->pos - 1]->type === TokenType::LeftParen
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

                    break;
            }

            if ($type === TokenType::LeftParen) {
                $this->parensBalance++;
            } elseif ($type === TokenType::RightParen) {
                $this->parensBalance--;
            }

            if ($this->parensBalance < 0) {
                throw new ParserException('Parse error. Unbalanced parentheses');
            }
        }

        if ($this->parensBalance > 0) {
            throw new ParserException('Parse error. Unbalanced parentheses');
        }

        if ($this->operandsAndOperators % 3 !== 0) {
            throw new ParserException('Parse error. Syntax error.');
        }

        return $this->tokens;
    }

    private function error(Token $token, string $msg): never
    {
        $position = $token->position + 1;

        throw new ParserException(
            "Parse error at position {$position}. {$msg}\n" .
                "Query: `{$this->query}`"
        );
    }
}
