<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Width extends Capability
{
	public function __construct(public readonly int $width) {}

	public function capabilities(): int
	{
		return Field::CAPABILITY_WIDTH;
	}
}
