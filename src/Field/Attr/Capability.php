<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Duon\Cms\Field\Field;

abstract class Capability
{
	abstract public function capabilities(): int;

	public function validate(Field $field): bool
	{
		$myCaps = $this->capabilities();

		return ($field->capabilities() & $myCaps) === $myCaps;
	}
}
