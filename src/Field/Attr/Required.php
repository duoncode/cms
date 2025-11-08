<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Requirable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Requirable) {
			$field->required(true);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . Requirable::class);
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Requirable) {
			return ['required' => $field->isRequired()];
		}

		return [];
	}
}
