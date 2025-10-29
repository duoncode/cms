<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Grid as GridValue;
use ValueError;

class Grid extends Field
{
	protected int $columns = 12;
	protected int $minCellWidth = 1;

	public const EXTRA_CAPABILITIES = Field::CAPABILITY_TRANSLATE | Field::CAPABILITY_COLUMNS;

	public function __toString(): string
	{
		return 'Grid Field';
	}

	public function columns(int $columns, int $minCellWidth = 1): static
	{
		if ($columns < 1 || $columns > 25) {
			throw new ValueError('The value of $columns must be >= 1 and <= 25');
		}

		if ($minCellWidth < 1 || $minCellWidth > $columns) {
			throw new ValueError('The value of $minCellWidth must be >= 1 and <= ' . (string) $columns);
		}

		$this->columns = $columns;
		$this->minCellWidth = $minCellWidth;

		return $this;
	}

	public function getColumns(): int
	{
		return $this->columns;
	}

	public function getMinCellWidth(): int
	{
		return $this->minCellWidth;
	}

	public function value(): GridValue
	{
		return new GridValue($this->node, $this, $this->valueContext);
	}

	public function properties(): array
	{
		return array_merge(parent::properties(), [
			'columns' => $this->columns,
			'minCellWidth' => $this->minCellWidth,
		]);
	}

	public function structure(mixed $value = null): array
	{
		$value = $value ?: $this->default;

		if (is_array($value)) {
			return ['type' => 'grid', 'columns' => 12, 'minCellWidth' => 1, 'value' => $value];
		}

		$result = ['type' => 'grid', 'columns' => 12, 'minCellWidth' => 1, 'value' => []];

		if ($this->translate) {
			foreach ($this->node->context->locales() as $locale) {
				$result['value'][$locale->id] = [];
			}
		}

		return $result;
	}
}
