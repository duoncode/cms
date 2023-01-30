<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

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
        $parser = new QueryParser($this->db, $this->builtins);

        return $this->build($parser->parse($query));
    }

    private function build(array $parserOutput): string
    {
        if (count($parserOutput) === 0) {
            return '';
        }

        $clause = '';

        foreach ($parserOutput as $output) {
            $clause = $output->get();
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
