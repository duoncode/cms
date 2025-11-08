<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value;

class Option extends Field implements Capability\Selectable
{
	use Capability\IsSelectable;

	protected bool $hasLabel = false;

	public function value(): Value\Option
	{
		return new Value\Option($this->node, $this, $this->valueContext);
	}

	public function properties(): array
	{
		$result = parent::properties();
		$result['hasLabel'] = $this->hasLabel;

		return $result;
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('option', $value);
	}
}
