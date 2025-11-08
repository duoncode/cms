<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Labelable;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Label implements Capability
{
	public function __construct(public readonly string $label) {}

	public function set(Field $field): void
	{
		if ($field instanceof Labelable) {
			$field->label($this->label);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . Labelable::class);
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Labelable) {
			return ['label' => $field->getLabel()];
		}

		return [];
	}
}
