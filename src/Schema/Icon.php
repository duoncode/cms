<?php

declare(strict_types=1);

namespace Duon\Cms\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
readonly class Icon
{
	public function __construct(
		public string $id,
		public ?string $color = null,
		public ?string $class = null,
		public ?string $style = null,
	) {}
}
