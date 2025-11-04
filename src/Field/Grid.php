<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Grid as GridValue;
use ValueError;

class Grid extends Field implements Capability\Translatable, Capability\GridResizable
{
	use Capability\IsTranslatable;
	use Capability\GridIsResizable;

	public function __toString(): string
	{
		return 'Grid Field';
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
			return ['type' => 'grid', 'columns' => $this->columns, 'minCellWidth' => $this->minCellWidth, 'value' => $value];
		}

		$result = ['type' => 'grid', 'columns' => $this->columns, 'minCellWidth' => $this->minCellWidth, 'value' => []];

		if ($this->translate) {
			foreach ($this->node->context->locales() as $locale) {
				$result['value'][$locale->id] = [];
			}
		}

		return $result;
	}
}
