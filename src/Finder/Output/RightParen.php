<?php

declare(strict_types=1);

namespace Duon\Cms\Finder\Output;

use Duon\Cms\Finder\Input\Token;

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
