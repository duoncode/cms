<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Requirable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Attr\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Required implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Requirable) {
			$field->required(true);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, Requirable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Requirable) {
			return ['required' => $field->isRequired()];
		}

		return [];
	}
}
