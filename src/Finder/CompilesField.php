<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder;

trait CompilesField
{
	private function compileField(
		string $fieldName,
		string $tableField,
		bool $asIs = false,
	): string {
		$parts = explode('.', $fieldName);
		$count = count($parts);
		$arrow = $asIs ? '->' : '->>';

		if ($count === 1) {
			return "{$tableField}->'{$parts[0]}'{$arrow}'value'";
		}

		$middle = implode("'->'", array_slice($parts, 0, $count - 1));
		$end = array_slice($parts, -1)[0];

		return "{$tableField}->'{$middle}'{$arrow}'{$end}'";
	}
}
