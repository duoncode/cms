<?php

declare(strict_types=1);

namespace Duon\Cms\Finder;

use Duon\Cms\Context;

final class QueryCompiler
{
	use CompilesField;

	public function __construct(
		private readonly Context $context,
		private readonly array $builtins,
	) {}

	public function compile(string $query): string
	{
		return $this->compileFragment($query)->sql;
	}

	public function compileFragment(string $query): SqlFragment
	{
		$parser = new QueryParser($this->context, $this->builtins);

		return $this->build($parser->parse($query));
	}

	private function build(array $parserOutput): SqlFragment
	{
		if (count($parserOutput) === 0) {
			return SqlFragment::empty();
		}

		$clause = '';

		foreach ($parserOutput as $output) {
			$clause .= $output->get();
		}

		return new SqlFragment($clause);
	}

	private function translateKeyword(string $keyword): string
	{
		return match ($keyword) {
			'now' => 'NOW()',
			'fulltext' => 'tsv websearch_to_tsquery',
		};
	}
}
