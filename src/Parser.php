<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Exception\RuntimeException;

class Parser
{
    public function parseQuery(string $query)
    {
        $query = trim($query);
        $isAnd = str_contains($query, '&&');
        $isOr = str_contains($query, '||');

        if ($isAnd && $isOr) {
            throw new RuntimeException('And (&&) and Or (||) queries cannot be used together');
        }

        $expressions = match (true) {
            $isAnd => explode('&&', $query),
            $isOr => explode('||', $query),
            default => [$query],
        };

        $pattern = '/^ *([A-Za-z0-9_-]+(\.[A-Za-z0-9_-]+)?) *(=|!=|>|<|>=|<=|LIKE) *([^ ]+|\'[^\']*?\'|"[^"]*?") *$/';
        $result = [
            'booleanOperator' => $isAnd ? 'AND' : ($isOr ? 'OR' : 'NONE'),
            'expressions' => [],
        ];

        foreach ($expressions as $expression) {
            if (preg_match($pattern, $expression, $matches)) {
                $result['expressions'][] = [
                    'left' => $matches[1],
                    'operator' => $matches[3],
                    'right' => trim(trim($matches[4], "'"), '"'),
                ];
            } else {
                throw new RuntimeException('Invalid query');
            }
        }

        return $result;
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
