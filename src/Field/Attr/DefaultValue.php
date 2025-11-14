<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Defaultable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Attr\capabilityErrorMessage;

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

		throw new RuntimeException(capabilityErrorMessage($field, Defaultable::class));
	}

	public function properties(Field $field): array
	{
		// DefaultValue is not serialized to frontend - it's used in structure() instead
		return [];
	}
}
