<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

class Category
{
	public function __construct(
		public readonly string $name,
		public readonly string $title,
		public readonly array $categories,
	) {}
}
