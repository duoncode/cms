<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\RuntimeException;

final class QueryCompiler
{
    public function __construct(public readonly array $builtins)
    {
    }
    public function compileQuery(string $query): string
    {
        $parser = new QueryParser($query);
        $ast = $parser->parse($query);

        return '';
    }

    public function parseOrder(string $statement)
    {
        $statement = trim($statement);

        $pattern = '/^ *([a-zA-Z0-9._-]+) *(ASC|DESC)? *$/';
        $fields = explode(',', $statement);
        $result = [];

        foreach ($fields as $field) {
            if (preg_match($pattern, trim($field), $matches)) {
                $result[] = [
                    'field' => $matches[1],
                    'direction' => $matches[2] ?? null ?: 'ASC',
                ];
            } else {
                throw new RuntimeException('Invalid query');
            }
        }

        return $result;
    }
}
