<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class QueryLexer
{
    private int $start = 0;
    private int $current = 0;

    /** @psalm-type list<Token> */
    private array $tokens = [];

    private readonly array $source;
    private readonly int $length;

    public function __construct(private readonly string $query)
    {
        $this->source = mb_str_split($query);
        $this->length = count($this->source);
    }

    public function tokens(): array
    {
        while (!$this->atEnd()) {
            $this->start = $this->current;
            $this->scan();
        }

        return $this->tokens;
    }

    private function scan(): void
    {
        $char = $this->advance();

        switch ($char) {
            case ' ':
            case "\t":
            case "\n":
            case "\r":
                break;
            case '(':
                $this->addParen(TokenType::LeftParen);
                break;
            case ')':
                $this->addParen(TokenType::RightParen);
                break;
            case '&':
                $this->addBooleanOperator(TokenType::And);
                break;
            case '|':
                $this->addBooleanOperator(TokenType::Or);
                break;
            case '=':
                $this->addOperator(TokenType::Equal);
                break;
            case '~':
                $this->addOperator(TokenType::Like);
                break;
            case '!':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenType::Unequal);
                } elseif ($this->matchNext('~')) {
                    $this->addOperator(TokenType::NotLike);
                } else {
                    throw new ParserException("Invalid operator '!'. " .
                        "It can only be used in combination with '=' and '~', i. e. '!=' and '!~'");
                }
                break;
            case '>':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenType::GreaterEqual);
                } else {
                    $this->addOperator(TokenType::Greater);
                }
                break;
            case '<':
                if ($this->matchNext('=')) {
                    $this->addOperator(TokenType::LessEqual);
                } else {
                    $this->addOperator(TokenType::Less);
                }
                break;
            case '"':
                // $this->validate();
                $this->consumeString();
                // $this->readyForParen = false;
                break;
            default:
                // $this->validate();
                // $this->readyForParen = false;
                if (is_numeric($char)) {
                    $this->consumeNumber();
                } elseif ($this->isIdentifier($char)) {
                    $this->consumeIdentifier();
                } else {
                    throw new ParserException("Unknown character '{$char}'");
                }
        }
    }

    private function addOperator(TokenType $type): void
    {
        $this->addToken(TokenGroup::Operator, $type);
    }

    private function addBooleanOperator(TokenType $type): void
    {
        $this->addToken(TokenGroup::BooleanOperator, $type);
    }

    private function addParen(TokenType $type): void
    {
        $this->addToken(TokenGroup::GroupSymbol, $type);
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
                $this->addToken(TokenGroup::Operand, TokenType::Identifier);

                break;
            }
        }
    }

    private function consumeNumber(): void
    {
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
                // $this->afterValue = true;
                $this->addToken(TokenGroup::Operand, TokenType::Number);
                break;
            }
        }
    }

    private function consumeString(): void
    {
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

        // $this->afterValue = true;
        $this->tokens[] = new Token(
            TokenGroup::Operand,
            TokenType::String,
            $this->start,
            str_replace('\\"', '"', $lexeme),
        );
    }

    private function addToken(TokenGroup $group, TokenType $type): void
    {
        $length = $this->current - $this->start;
        $slice = array_slice($this->source, $this->start, $length);
        $lexeme = implode('', $slice);

        $this->tokens[] = new Token($group, $type, $this->start, $lexeme);
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
}
