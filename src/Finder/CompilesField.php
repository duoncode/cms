<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

trait CompilesField
{
    private function compileField(string $fieldName, string $tableField): string
    {
        $parts = explode('.', $fieldName);
        $count = count($parts);

        if ($count === 1) {
            return "{$tableField}->'{$parts[0]}'->>'value'";
        }

        $middle = implode("'->'", array_slice($parts, 0, $count - 1));
        $end = array_slice($parts, -1)[0];

        return "{$tableField}->'{$middle}'->>'{$end}'";
    }
}
