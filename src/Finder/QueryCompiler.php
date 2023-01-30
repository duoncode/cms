<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\RuntimeException;
use Conia\Quma\Database;

final class QueryCompiler
{
    use CompilesField;

    public function __construct(
        private readonly Database $db,
        public readonly array $builtins
    ) {
    }

    public function compile(string $query): string
    {
        $parser = new QueryParser(array_keys($this->builtins));

        return $this->build($parser->parse($query));
    }

    private function build(array $items): string
    {
        if (count($items) === 0) {
            return '';
        }

        $clause = '';

        foreach ($items as $item) {
            if ($item instanceof Condition) {
                $clause = $item->print();
            } else {
                $clause .= match ($item->type) {
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
                    TokenType::Boolean => strtolower($item->lexeme),
                    TokenType::Field => $this->compileField($item->lexeme, 'p.content'),
                    TokenType::Builtin => $this->builtins[$item->lexeme],
                    TokenType::Keyword => $this->translateKeyword($item->lexeme),
                    TokenType::Null => 'NULL',
                    TokenType::Number => $item->lexeme,
                    TokenType::String => $this->db->quote($item->lexeme),
                };
            }
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
