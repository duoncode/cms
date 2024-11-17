<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Field;

use FiveOrbs\Cms\Value\DateTime as DateTimeValue;

class DateTime extends Field
{
	public function value(): DateTimeValue
	{
		return new DateTimeValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('datetime', $value);
	}
}
