<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Number as NumberValue;

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
