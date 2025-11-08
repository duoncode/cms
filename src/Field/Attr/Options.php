<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Selectable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Options implements Capability
{
	public function __construct(public readonly array $options) {}

	public function set(Field $field): void
	{
		if ($field instanceof Selectable) {
			$field->options($this->options);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . Selectable::class);
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Selectable) {
			return ['options' => $field->getOptions()];
		}

		return [];
	}
}
