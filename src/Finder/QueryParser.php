<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;
use Conia\Core\Exception\ParserOutputException;
use Conia\Core\Finder\Input\Token;
use Conia\Core\Finder\Input\TokenGroup;
use Conia\Core\Finder\Input\TokenType;
use Conia\Core\Finder\Output\Comparison;
use Conia\Core\Finder\Output\Exists;
use Conia\Core\Finder\Output\Expression;
use Conia\Core\Finder\Output\LeftParen;
use Conia\Core\Finder\Output\Operator;
use Conia\Core\Finder\Output\RightParen;
use Conia\Core\Finder\Output\UrlPath;

final class QueryParser
{
    /** @psalm-type list<Token> */
    private array $tokens;

    private int $pos;
    private int $length;
    private int $parensBalance;
    private bool $readyForCondition = true;
    private string $query;

    /**
     * @psalm-param list<string> $builtins
     */
    public function __construct(
        private readonly Context $context,
        private readonly array $builtins = []
    ) {
    }

    /**
     * Returns an array of output tokens which can be translated to a
     * valid SQL WHERE expression.
     */
    public function parse(string $query): array
    {
        $this->query = $query;
        $this->tokens = (new QueryLexer(array_keys($this->builtins)))->tokens($query);
        $this->length = count($this->tokens);

        $this->parensBalance = 0;
        $this->readyForCondition = true;
        $this->pos = 0;

        while ($this->pos < $this->length) {
            try {
                $token = $this->tokens[$this->pos];

                $result[] = match ($token->group) {
                    TokenGroup::Operand => $this->getExpression($token),
                    TokenGroup::BooleanOperator => $this->getBooleanOperator($token),
                    TokenGroup::LeftParen => $this->getLeftParen($token),
                    TokenGroup::RightParen => $this->getRightParen($token),

                    // Special case Operator:
                    // As we consume operators together with operands, it would
                    // be invalid if we would find operators anywhere else.
                    TokenGroup::Operator => $this->error($token, 'Invalid position for an operator.'),
                };

                if ($this->parensBalance < 0) {
                    $this->error($token, 'Parse error. Unbalanced parenthesis');
                }
            } catch (ParserOutputException $e) {
                $this->error($e->token, $e->getMessage());
            }
        }

        if ($this->parensBalance > 0) {
            $this->error($token, 'Parse error. Unbalanced parenthesis');
        }

        return $result;
    }

    /**
     * @throws ParserException
     */
    private function getExpression(Token $token): Expression
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

    private function getComparisonCondition(Token $left): Expression
    {
        $operator = $this->tokens[$this->pos + 1];
        $right = $this->tokens[$this->pos + 2];

        // Advance 3 steps: operand operator operand
        $this->pos += 3;
        // Wrong position to start a new condition after this one
        $this->readyForCondition = false;

        if ($left->type === TokenType::Path || $right->type === TokenType::Path) {
            return new UrlPath($left, $operator, $right);
        }

        return new Comparison($left, $operator, $right, $this->context, $this->builtins);
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

        $this->readyForCondition = false;
        $this->pos++;

        return new Exists($token);
    }

    /**
     * @throws ParserException
     */
    private function getBooleanOperator(Token $token): Operator
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

        return new Operator($token);
    }

    /**
     * @throws ParserException
     */
    private function getLeftParen(Token $token): LeftParen
    {
        if (!$this->readyForCondition) {
            $this->error($token, 'Invalid position for parenthesis.');
        }

        $this->parensBalance++;
        $this->pos++;

        return new LeftParen($token);
    }

    /**
     * @throws ParserException
     */
    private function getRightParen(Token $token): RightParen
    {
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
        $this->parensBalance--;
        $this->pos++;

        return new RightParen($token);
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
