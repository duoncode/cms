<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

final class OrderCompiler
{
    public function __construct(private readonly array $builtins = [])
    {
    }

    public function compile(string $statement): string
    {
        $parsed = $this->parse($statement);

        if (count($parsed) === 0) {
            return '';
        }

        $expressions = [];

        foreach ($parsed as $field) {
            $fieldName = $field['field'];
            $expression = $this->builtins[$fieldName] ?? null;

            if (!$expression) {
                $expression = $this->getJsonAccessor($fieldName);
            }

            $expressions[] = $expression . ' ' . $field['direction'];
        }

        if (count($expressions) > 0) {
            return "\nORDER BY\n    " . implode(",\n    ", $expressions);
        }

        return '';
    }

    private function parse(string $statement): array
    {
        $fields = explode(',', $statement);
        $pattern = '/^\s*([a-zA-Z][a-zA-Z0-9._]*)\s*(asc|desc)?\s*$/i';
        $result = [];

        foreach ($fields as $field) {
            if (preg_match($pattern, trim($field), $matches)) {
                $result[] = [
                    'field' => $matches[1],
                    'direction' => strtoupper($matches[2] ?? null ?: 'ASC'),
                ];
            } else {
                throw new ParserException('Invalid query');
            }
        }

        return $result;
    }

    private function getJsonAccessor(string $fieldName): string
    {
        $parts = $this->getParts($fieldName);
        $count = count($parts);

        if ($count === 1) {
            return "p.content->'{$parts[0]}'->>'value'";
        }

        $middle = implode("'->'", array_slice($parts, 0, $count - 1));
        $end = array_slice($parts, -1)[0];

        return "p.content->'{$middle}'->>'{$end}'";
    }

    private function getParts(string $fieldName): array
    {
        if (str_ends_with($fieldName, '.')) {
            throw new ParserException('Invalid query');
        }

        return explode('.', $fieldName);
    }
}
