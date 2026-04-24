<?php

declare(strict_types=1);

namespace Duon\Cms\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class Icon
{
	/** @param array<array-key, mixed> $args */
	public function __construct(
		public string $id,
		public array $args = [],
	) {}
}
