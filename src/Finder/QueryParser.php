<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class QueryParser
{
    /** @psalm-type list<Token> */
    private array $tokens;

    private int $pos;
    private int $length;
    private bool $readyForCondition = true;
    private string $query;

    /**
     * @psalm-param list<string> $builtins
     */
    public function __construct(private readonly array $builtins = [])
    {
    }

    /**
     * Returns a stream of tokens which can be translated to a
     * valid SQL WHERE expression.
     *
     * Does not transform the token stream with one exception: if an operand
     * is not part of an comparison (e. g. `field = 'string'`), is of type
     * Field and is the sole part on one side of a boolean expression, it will
     * be transformed to token type Exists.
     *
     * Example:  field1 >= 1 & content1 & field2 = 'string'
     *                         ^^^^^^^^
     *           content1 will be set to token type Exists
     *
     * All other tokens are simply checked if they are in the correct position.
     */
    public function parse(string $query): array
    {
        $this->query = $query;
        $this->tokens = (new QueryLexer($this->builtins))->tokens($query);
        $this->length = count($this->tokens);

        $parensBalance = 0;
        $this->readyForCondition = true;
        $this->pos = 0;

        while ($this->pos < $this->length) {
            $token = $this->tokens[$this->pos];
            $type = $token->type;

            switch ($token->group) {
                case TokenGroup::Operand:
                    $result[] = $this->getCondition($token);
                    break;
                case TokenGroup::Operator:
                    // As we consume operators together with operands, it would
                    // be invalid if we would find operators anywhere else.
                    $this->error($token, 'Invalid position for an operator.');

                    break;
                case TokenGroup::BooleanOperator:
                    $result[] = $this->getBooleanOperator($token);
                    break;
                case TokenGroup::GroupSymbol:
                    $result[] = $this->getGroup($type, $token);
                    break;
            }

            if ($type === TokenType::LeftParen) {
                $parensBalance++;
            } elseif ($type === TokenType::RightParen) {
                $parensBalance--;
            }

            if ($parensBalance < 0) {
                $this->error($token, 'Parse error. Unbalanced parenthesis');
            }
        }

        if ($parensBalance > 0) {
            $this->error($token, 'Parse error. Unbalanced parenthesis');
        }

        return $result;
    }

    /**
     * @throws ParserException
     */
    private function getCondition(Token $token): Condition
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
            // A Regular key value comparision
            return $this->getComparisonCondition($token);
        }

        if (
            ($this->pos + 2 <= $this->length
            && $this->tokens[$this->pos + 1]->group === TokenGroup::BooleanOperator)
            || count($this->tokens) === $this->pos + 1
        ) {
            // Key exists query
            return $this->getExistsCondition($token);
        }

        if (
            $this->tokens[$this->pos + 1]->group === TokenGroup::Operator
            && $this->tokens[$this->pos + 2]->group === TokenGroup::Operator
        ) {
            $this->error($token, 'Multiple operators. Maybe you used == instead of =.');
        }

        $this->error($token, 'Invalid condition.');
    }

    private function getComparisonCondition(Token $left): Condition
    {
        $operator = $this->tokens[$this->pos + 1];
        $right = $this->tokens[$this->pos + 2];

        // Advance 3 steps: operand operator operand
        $this->pos += 3;
        // Wrong position to start a new condition after this one
        $this->readyForCondition = false;

        if ($left->type === TokenType::Path || $left->type === TokenType::Path) {
            return new UrlPath($left, $operator, $right);
        }

        return new Comparison($left, $operator, $right);
    }

    private function getExistsCondition(Token $token): Exists
    {
        if ($token->type !== TokenType::Field) {
            $this->error(
                $token,
                'Conditions of type `field exists` must consist of ' .
                'a single operand of type Field.'
            );
        }

        // We advance two steps as we checked the BooleanOperator already
        $this->pos += 2;
        // After the BooleanOperator a new condition can be started
        $this->readyForCondition = true;

        return new Exists($token);
    }

    /**
     * @throws ParserException
     */
    private function getBooleanOperator(Token $token): Token
    {
        if ($this->readyForCondition) {
            $this->error(
                $token,
                'Invalid position for a boolean operator. ' .
                    'Maybe you used && instead of & or || instead of |'
            );
        }

        if ($this->pos >= $this->length - 1) {
            $this->error($token, 'Boolean operator at the end of the expression.');
        }

        $this->readyForCondition = true;
        $this->pos++;

        return $token;
    }

    /**
     * @throws ParserException
     */
    private function getGroup(TokenType $type, Token $token): Token
    {
        if ($type === TokenType::RightParen) {
            if (
                $this->pos > 0
                && $this->tokens[$this->pos - 1]->type === TokenType::LeftParen
            ) {
                $this->error(
                    $token,
                    'Invalid parenthesis: empty group.'
                );
            }

            $this->readyForCondition = false;
        } else {
            if (!$this->readyForCondition) {
                $this->error($token, 'Invalid position for parenthesis.');
            }
        }

        $this->pos++;

        return $token;
    }

    /**
     * @throws ParserException
     */
    private function error(Token $token, string $msg): never
    {
        $position = $token->position + 1;

        if ($this->pos === count($this->tokens)) {
            // This is a general error. We are after the last token.
            $start = 8;
            $len = strlen($this->query);
        } else {
            $start = $position + 7;
            $len = $token->len();
        }

        throw new ParserException(
            "Parse error at position {$position}. {$msg}\n\n" .
                "Query: `{$this->query}`\n" .
                str_repeat(' ', $start) .
                str_repeat('^', $len) . "\n\n"
        );
    }
}
