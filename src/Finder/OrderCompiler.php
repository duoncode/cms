<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder;

use FiveOrbs\Cms\Exception\ParserException;

final class OrderCompiler
{
	use CompilesField;

	public function __construct(private readonly array $builtins = []) {}

	public function compile(string $statement): string
	{
		if (empty(trim($statement))) {
			throw new ParserException('Empty order by clause');
		}

		$parsed = $this->parse($statement);

		if (count($parsed) === 0) {
			return '';
		}

		$expressions = [];

		foreach ($parsed as $field) {
			$fieldName = $field['field'];
			$expression = $this->builtins[$fieldName] ?? null;

			if (!$expression) {
				$expression = $this->compileField($fieldName, 'n.content', asIs: true);
			}

			$expressions[] = $expression . ' ' . $field['direction'];
		}

		if (count($expressions) > 0) {
			return "\n    " . implode(",\n    ", $expressions);
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
}
