<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Immutable extends Capability
{
	public function capabilities(): int
	{
		return Field::CAPABILITY_IMMUTABLE;
	}
} // We can't use Readonly as it is a keyword of PHP
