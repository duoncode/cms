<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Output;

use FiveOrbs\Cms\Finder\Input\Token;

class RightParen implements Output
{
	public function __construct(
		public Token $token,
	) {}

	public function get(): string
	{
		return ')';
	}
}
