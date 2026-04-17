<?php

declare(strict_types=1);

namespace Duon\Cms;

final class Section
{
	public function __construct(
		private readonly string $name,
	) {}

	public function name(): string
	{
		return $this->name;
	}
}
