<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\RuntimeException;
use Conia\Quma\Database;

final class QueryCompiler
{
    use CompilesJsonAccessor;

    public function __construct(
        private readonly Database $db,
        public readonly array $builtins
    ) {
    }

    public function compile(string $query): string
    {
        $parser = new QueryParser(array_keys($this->builtins));
        $tokens = $parser->parse($query);

        return $this->build($tokens);
    }

    private function build(array $tokens): string
    {
        if (count($tokens) === 0) {
            return '';
        }

        $clause = '';

        foreach ($tokens as $token) {
            $clause .= match ($token->type) {
                TokenType::LeftParen => '(',
                TokenType::RightParen => '(',
                TokenType::Equal => ' = ',
                TokenType::Greater => ' > ',
                TokenType::GreaterEqual => ' >= ',
                TokenType::Less => ' < ',
                TokenType::LessEqual => ' <=',
                TokenType::Like => ' LIKE ',
                TokenType::Unequal => ' !=',
                TokenType::Unlike => ' NOT LIKE ',
                TokenType::And => ' AND ',
                TokenType::Or => ' OR ',
                TokenType::Boolean => strtolower($token->lexeme),
                TokenType::Field => $this->compileJsonAccessor($token->lexeme, 'p.content'),
                TokenType::Builtin => $this->builtins[$token->lexeme],
                TokenType::Keyword => $this->translateKeyword($token->lexeme),
                TokenType::Null => 'NULL',
                TokenType::Number => $token->lexeme,
                TokenType::String => $this->db->quote($token->lexeme),
            };
        }

        return $clause;
    }

    private function translateKeyword(string $keyword): string
    {
        return match ($keyword) {
            'now' => 'NOW()',
            'fulltext' => 'tsv websearch_to_tsquery',
        };
    }
}
