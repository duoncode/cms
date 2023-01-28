<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Exception\ParserException;

trait CompilesJsonAccessor
{
    private function compileJsonAccessor(string $fieldName, string $tableField): string
    {
        $parts = $this->getParts($fieldName);
        $count = count($parts);

        if ($count === 1) {
            return "{$tableField}->'{$parts[0]}'->>'value'";
        }

        $middle = implode("'->'", array_slice($parts, 0, $count - 1));
        $end = array_slice($parts, -1)[0];

        return "{$tableField}->'{$middle}'->>'{$end}'";
    }

    private function getParts(string $fieldName): array
    {
        if (str_ends_with($fieldName, '.')) {
            throw new ParserException('Invalid query');
        }

        return explode('.', $fieldName);
    }
}
