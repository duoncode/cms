<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Label extends Capability
{
	public function __construct(public readonly string $label) {}

	public function capabilities(): int
	{
		return Field::CAPABILITY_LABEL;
	}
}
