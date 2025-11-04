<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\GridResizable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Columns implements Capability
{
	public function __construct(
		public readonly int $columns,
		public readonly int $minCellWidth = 1,
	) {}

	public function set(Field $field): void
	{
		if ($field instanceof GridResizable) {
			$field->columns($this->columns, $this->minCellWidth);
			return;
		}

		$cap = GridResizable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
