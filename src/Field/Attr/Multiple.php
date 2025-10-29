<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Multiple extends Capability
{
	public function capabilities(): int
	{
		return Field::CAPABILITY_MULTIPLE;
	}
}
