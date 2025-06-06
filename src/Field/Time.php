<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Value\Time as TimeValue;

class Time extends Field
{
	public function value(): TimeValue
	{
		return new TimeValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('time', $value);
	}
}
