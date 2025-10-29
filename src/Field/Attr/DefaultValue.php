<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultValue extends Capability
{
	public function __construct(protected readonly mixed $default) {}

	public function get(): mixed
	{
		if (is_callable($this->default)) {
			return ($this->default)();
		}

		return $this->default;
	}

	public function capabilities(): int
	{
		return Field::CAPABILITY_DEFAULT_VALUE;
	}
}
