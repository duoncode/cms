<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\GridResizable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

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

		throw new RuntimeException(capabilityErrorMessage($field, GridResizable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof GridResizable) {
			return [
				'columns' => $field->getColumns(),
				'minCellWidth' => $field->getMinCellWidth(),
			];
		}

		return [];
	}
}
