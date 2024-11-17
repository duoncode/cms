<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Field;

use FiveOrbs\Cms\Field\Field;
use FiveOrbs\Cms\Value\Number as NumberValue;

class Number extends Field
{
	public function value(): NumberValue
	{
		return new NumberValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('number', $value);
	}
}
