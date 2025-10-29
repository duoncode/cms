<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Columns extends Capability
{
	public function __construct(
		public readonly int $columns,
		public readonly int $minCellWidth = 1,
	) {}

	public function capabilities(): int
	{
		return Field::CAPABILITY_COLUMNS;
	}
}
