<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Description extends Capability
{
	public function __construct(public readonly string $description) {}

	public function capabilities(): int
	{
		return Field::CAPABILITY_DESCRIPTION;
	}
}
