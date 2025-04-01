<?php

declare(strict_types=1);

namespace Duon\Cms\Finder\Output;

use Duon\Cms\Context;
use Duon\Cms\Exception\ParserOutputException;
use Duon\Cms\Finder\Input\Token;
use Duon\Cms\Finder\Input\TokenType;

final readonly class NullComparison extends Expression implements Output
{
	public function __construct(
		private Token $left,
		private Token $operator,
		private Token $right,
		private Context $context,
		private array $builtins,
	) {}

	public function get(): string
	{
		switch ($this->operator->type) {
			case TokenType::Equal:
				return $this->getSqlExpression(true);
			case TokenType::Unequal:
				return $this->getSqlExpression(false);
		}

		throw new ParserOutputException(
			$this->operator,
			'Only equal (=) or unequal (!=) operators are allowed in queries with an null value.',
		);
	}

	private function getSqlExpression(bool $equal): string
	{
		return sprintf(
			'%s %s %s',
			$this->getOperand($this->left, $this->context->db, $this->builtins),
			$equal ? 'IS' : 'IS NOT',
			$this->getOperand($this->right, $this->context->db, $this->builtins),
		);
	}
}
