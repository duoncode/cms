<?php

declare(strict_types=1);

namespace Duon\Cms\Finder;

use Duon\Cms\Context;
use Duon\Cms\Finder\Condition\ExpressionCompiler;

final class QueryCompiler
{
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

		$compiler = new ExpressionCompiler($this->context, $this->builtins);
		$clause = '';

		foreach ($parserOutput as $output) {
			$clause .= $compiler->compile($output);
		}

		return new SqlFragment($clause);
	}
}
