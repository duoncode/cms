<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class QueryParser
{
    private int $start = 0;
    private int $current = 0;

    /** @psalm-type list<Token> */
    private array $tokens = [];

    private bool $leftHand = true;
    private bool $afterValue = false;
    private bool $readyForParen = true; // Initialize with true to allow starting with an opening paren
    private bool $readyForSubExpression = true;
    private readonly array $source;
    private readonly int $length;

    public function __construct(private readonly string $query)
    {
        $this->source = mb_str_split($query);
        $this->length = count($this->source);
    }

    public function tokens(): array
    {
        $tokens = $this->scan();

        $i = 0;
        $operandsAndOperators = 0;

        foreach ($tokens as $token) {
            if ($token->kind === TokenKind::LeftParen) {
                $i++;
            } elseif ($token->kind === TokenKind::RightParen) {
                $i--;
            }

            if ($i < 0) {
                throw new ParserException('Parse error. Unbalanced parentheses');
            }

            if ($token->group !== TokenGroup::BooleanSymbol) {
                $operandsAndOperators++;
            }
        }

        if ($i > 0) {
            throw new ParserException('Parse error. Unbalanced parentheses');
        }

        if ($operandsAndOperators % 3 !== 0) {
            throw new ParserException('Parse error. Syntax error.');
        }

        return $tokens;
    }

    private function scan(): array
    {
        while (!$this->atEnd()) {
            $this->start = $this->current;
            $this->scanToken();
        }

        return $this->tokens;
    }

    private function scanToken(): void
    {
        $char = $this->advance();

        switch ($char) {
            case ' ':
            case "\t":
                break;
            case '(':
                if ($this->readyForParen) {
                    $this->startSubExpression();
                    $this->addParen(TokenKind::LeftParen);
                } else {
                    throw new ParserException($this->errorMessage('Wrong parenthesis position.'));
                }
                break;
            case ')':
                $this->addParen(TokenKind::RightParen);
                $this->readyForParen = false;
                $this->readyForSubExpression = false;
                break;
            case '&':
                $this->addBooleanOperator(TokenKind::And);
                break;
            case '|':
                $this->addBooleanOperator(TokenKind::Or);
                break;
            case '=':
                $this->addOperator(TokenKind::Equal);
                break;
            case '~':
                $this->addOperator(TokenKind::Like);
                break;
            case '!':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenKind::Unequal);
                } elseif ($this->matchNext('~')) {
                    $this->addOperator(TokenKind::NotLike);
                } else {
                    throw new ParserException("Invalid operator '!'. " .
                        "It can only be used in combination with '=' and '~', i. e. '!=' and '!~'");
                }
                break;
            case '>':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenKind::GreaterEqual);
                } else {
                    $this->addOperator(TokenKind::Greater);
                }
                break;
            case '<':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenKind::LessEqual);
                } else {
                    $this->addOperator(TokenKind::Less);
                }
                break;
            case '"':
                $this->validate();
                $this->consumeString();
                $this->readyForParen = false;
                break;
            default:
                $this->validate();
                $this->readyForParen = false;
                if (is_numeric($char)) {
                    $this->consumeNumber();
                } elseif ($this->isIdentifier($char)) {
                    $this->consumeIdentifier();
                } else {
                    throw new ParserException("Unknown character '{$char}'");
                }
        }
    }

    private function addOperator(TokenKind $kind): void
    {
        if (!$this->leftHand) {
            throw new ParserException(
                $this->errorMessage("Multiple operators. E. g. '==' instead of '='")
            );
        }

        $this->leftHand = false;
        $this->afterValue = false;
        $this->readyForParen = false;
        $this->addToken(TokenGroup::Operator, $kind);
    }

    private function addBooleanOperator(TokenKind $kind): void
    {
        $this->leftHand = true;
        $this->afterValue = false;
        $this->readyForParen = true;
        $this->readyForSubExpression = true;
        $this->addToken(TokenGroup::BooleanSymbol, $kind);
        $this->startSubExpression();
    }

    private function addParen(TokenKind $kind): void
    {
        $this->leftHand = true;
        $this->afterValue = false;
        $this->addToken(TokenGroup::BooleanSymbol, $kind);
    }

    private function isIdentifier(string $char): bool
    {
        return ctype_alpha($char);
    }

    private function consumeIdentifier(): void
    {
        while (true) {
            $char = $this->peek();
            $valid = ctype_alpha($char) || ctype_digit($char) || $char === '_' || $char === '.';

            if ($valid && !$this->atEnd()) {
                $this->advance();
            } else {
                $this->afterValue = true;

                if ($this->leftHand) {
                    $this->addToken(TokenGroup::Operand, TokenKind::Field);
                } else {
                    $this->addToken(TokenGroup::Operand, TokenKind::Identifier);
                }

                break;
            }
        }
    }

    private function consumeNumber(): void
    {
        $this->validateSide(
            $this->errorMessage('Numbers are only allowed on the right hand side of an expression')
        );

        $hasDot = false;

        while (true) {
            $char = $this->peek();
            $isDot = $char === '.';

            if ((is_numeric($this->peek()) || $isDot) && !$this->atEnd()) {
                if ($isDot) {
                    if ($hasDot) {
                        throw new ParserException('Number with multiple dots');
                    }

                    $hasDot = true;
                }

                $this->advance();
            } else {
                $this->afterValue = true;
                $this->addToken(TokenGroup::Operand, TokenKind::Number);
                break;
            }
        }
    }

    private function consumeString(): void
    {
        $this->validateSide('Strings are only allowed on the right hand side of an expression');

        while ($this->peek() !== '"' && !$this->atEnd()) {
            if ($this->peek() === '\\' && $this->peekNext() === '"') {
                $this->advance();
            }

            $this->advance();
        }

        if ($this->atEnd()) {
            throw new ParserException('Unterminated string');
        }

        // Hop to the closing "
        $this->advance();

        if ($this->start === $this->current) {
            $lexeme = '';
        } else {
            $start = $this->start + 1;
            $length = $this->current - $this->start - 2;
            $slice = array_slice($this->source, $start, $length);
            $lexeme = implode('', $slice);
        }

        $this->afterValue = true;
        $this->tokens[] = new Token(
            TokenGroup::Operand,
            TokenKind::String,
            str_replace('\\"', '"', $lexeme),
        );
    }

    private function startSubExpression(): void
    {
        if (!$this->readyForSubExpression) {
            throw new ParserException(
                $this->errorMessage(
                    'Wrong position to start a sub expression.'
                )
            );
        }

        $this->leftHand = true;
        $this->afterValue = false;
    }

    private function validate(): void
    {
        if ($this->afterValue) {
            throw new ParserException(
                $this->errorMessage(
                    'Multiple values on on side of and expression. Like `field1 "string" = 1`.'
                )
            );
        }

        if (!$this->readyForSubExpression) {
            throw new ParserException(
                $this->errorMessage(
                    'Invalid position to start a sub expression.'
                )
            );
        }
    }
    private function validateSide(string $msg): void
    {
        if ($this->leftHand) {
            throw new ParserException($this->errorMessage($msg));
        }
    }

    private function addToken(TokenGroup $group, TokenKind $kind): void
    {
        $start = $this->start;
        $length = $this->current - $this->start;
        $slice = array_slice($this->source, $start, $length);
        $lexeme = implode('', $slice);

        $this->tokens[] = new Token($group, $kind, $lexeme);
    }

    private function advance(): string
    {
        $result = $this->source[$this->current];
        $this->current++;

        return $result;
    }

    private function peek(): string
    {
        if ($this->atEnd()) {
            return '';
        }

        return $this->source[$this->current];
    }

    private function peekNext(): string
    {
        if ($this->current + 1 > $this->length - 1) {
            return '';
        }

        return $this->source[$this->current + 1];
    }

    private function matchNext(string $expected): bool
    {
        if ($this->atEnd()) {
            return false;
        }

        if ($this->source[$this->current] === $expected) {
            $this->current++;

            return true;
        }

        return false;
    }

    private function atEnd(): bool
    {
        return $this->current > $this->length - 1;
    }

    private function errorMessage($msg): string
    {
        return "Parse error at position {$this->current}. {$msg}\n" .
            "Query: `{$this->query}`";
    }
}
