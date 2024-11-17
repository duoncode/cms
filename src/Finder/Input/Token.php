<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Input;

readonly class Token
{
	public function __construct(
		public TokenGroup $group,
		public TokenType $type,
		public int $position,
		public string $lexeme,
	) {}

	public function len(): int
	{
		return strlen($this->lexeme);
	}
}
