<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Output;

use FiveOrbs\Cms\Finder\Input\Token;

final readonly class UrlPath extends Expression implements Output
{
	public function __construct(
		public Token $left,
		public Token $operator,
		public Token $right,
	) {}

	public function get(): string
	{
		return '';
	}
}
