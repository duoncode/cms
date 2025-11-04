<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Defaultable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultValue implements Capability
{
	public function __construct(protected readonly mixed $default) {}

	public function get(): mixed
	{
		if (is_callable($this->default)) {
			return ($this->default)();
		}

		return $this->default;
	}

	public function set(Field $field): void
	{
		if ($field instanceof Defaultable) {
			$field->default($this->get());
			return;
		}

		$cap = Defaultable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
