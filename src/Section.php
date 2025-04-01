<?php

declare(strict_types=1);

namespace Duon\Cms;

class Section
{
	public function __construct(
		public readonly string $name,
	) {}
}
