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

    private readonly string $query;
    private readonly array $source;
    private readonly int $length;

    public function __construct(private readonly array $builtins = [])
    {
    }

    public function tokens(string $query): array
    {
        $this->query = $query;
        $this->source = mb_str_split($query);
        $this->length = count($this->source);

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
                    $this->addOperator(TokenType::Unlike);
                } else {
                    $this->error(
                        "Invalid operator '!'. " .
                        "It can only be used in combination with '=' " .
                        "and '~', i. e. '!=' and '!~'"
                    );
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
            case "'":
                // $this->validate();
                $this->consumeString($char);
                // $this->readyForParen = false;
                break;
            default:
                // $this->validate();
                // $this->readyForParen = false;
                if (is_numeric($char) || $char === '-') {
                    $this->consumeNumber($char);
                } elseif ($this->isIdentifier($char)) {
                    $this->consumeIdentifier();
                } else {
                    $this->error("Syntax error, unknown character '{$char}'");
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
                $lexeme = $this->getLexeme();
                $type = $this->getIdentifierType($lexeme);
                $this->tokens[] = new Token(TokenGroup::Operand, $type, $this->start, $lexeme);

                break;
            }
        }
    }

    private function consumeNumber(string $char): void
    {
        if ($char === '-') {
            if (!is_numeric($this->peek())) {
                $this->error("Syntax error, unknown character '-'");
            }
        }

        while (is_numeric($this->peek())) {
            $this->advance();
        }

        if ($this->peek() === '.') {
            $this->advance();
            $hasFraction = false;

            while (is_numeric($this->peek())) {
                $hasFraction = true;
                $this->advance();
            }

            if (!$hasFraction) {
                $this->error('Invalid number.');
            }
        }

        $this->addToken(TokenGroup::Operand, TokenType::Number);
    }

    private function consumeString(string $char): void
    {
        while ($this->peek() !== $char && !$this->atEnd()) {
            if ($this->peek() === '\\' && $this->peekNext() === $char) {
                $this->advance();
            }

            $this->advance();
        }

        if ($this->atEnd()) {
            $this->error('Unterminated string.');
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
            str_replace('\\' . $char, $char, $lexeme),
        );
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

    private function addToken(TokenGroup $group, TokenType $type): void
    {
        $lexeme = $this->getLexeme();
        $this->tokens[] = new Token($group, $type, $this->start, $lexeme);
    }

    private function getLexeme(): string
    {
        $length = $this->current - $this->start;
        $slice = array_slice($this->source, $this->start, $length);

        return implode('', $slice);
    }

    private function getIdentifierType(string $lexeme): TokenType
    {
        switch ($lexeme) {
            case 'true':
            case 'false':
                return TokenType::Boolean;
            case 'null':
                return TokenType::Null;
            case 'now':
            case 'fulltext':
                return TokenType::Keyword;
            default:
                if (in_array($lexeme, $this->builtins)) {
                    return TokenType::Field;
                }

                return TokenType::Content;
        }
    }

    /**
     * @throws ParserException
     */
    private function error(string $msg): never
    {
        throw new ParserException(
            "Parse error at position {$this->start}. {$msg}\n\n" .
                "Query: `{$this->query}`\n" .
                str_repeat(' ', $this->start + 8) .
                str_repeat('^', $this->current - $this->start) . "\n\n"
        );
    }
}
