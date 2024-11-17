<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Finder\Output;

use FiveOrbs\Cms\Finder\Input\Token;

final readonly class Exists extends Expression implements Output
{
	public function __construct(
		private Token $token,
	) {}

	public function get(): string
	{
		return '';
	}
}
