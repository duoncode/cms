<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Finder\CompilesJsonAccessor;
use Conia\Core\Finder\Input\Token;
use Conia\Core\Finder\Input\TokenType;
use Conia\Quma\Database;

abstract readonly class Condition
{
    use CompilesJsonAccessor;

    private Database $db;
    private array $builtins;

    private function getOperator(TokenType $type): string
    {
        return match ($type) {
            TokenType::LeftParen => '(',
            TokenType::RightParen => ')',
            TokenType::Equal => ' = ',
            TokenType::Greater => ' > ',
            TokenType::GreaterEqual => ' >= ',
            TokenType::Less => ' < ',
            TokenType::LessEqual => ' <=',
            TokenType::Like => ' LIKE ',
            TokenType::ILike => ' ILIKE ',
            TokenType::Unequal => ' !=',
            TokenType::Unlike => ' NOT LIKE ',
            TokenType::IUnlike => ' NOT ILIKE ',
            TokenType::And => ' AND ',
            TokenType::Or => ' OR ',
        };
    }

    private function getOperand(Token $token): string
    {
        return match ($token) {
            TokenType::Boolean => strtolower($token->lexeme),
            TokenType::Field => $this->compileJsonAccessor($token->lexeme, 'p.content'),
            TokenType::Builtin => $this->builtins[$token->lexeme],
            TokenType::Keyword => $this->translateKeyword($token->lexeme),
            TokenType::Null => 'NULL',
            TokenType::Number => $token->lexeme,
            TokenType::String => $this->db->quote($token->lexeme),
        };
    }

    private function translateKeyword(string $keyword): string
    {
        return match ($keyword) {
            'now' => 'NOW()',
            'fulltext' => 'tsv websearch_to_tsquery',
        };
    }
}
