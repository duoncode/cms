<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultValue
{
	public function __construct(protected readonly mixed $default) {}

	public function get(): mixed
	{
		if (is_callable($this->default)) {
			return ($this->default)();
		}

		return $this->default;
	}
}
