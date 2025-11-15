<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Hidable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Hidden implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Hidable) {
			$field->hidden(true);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, Hidable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Hidable) {
			return ['hidden' => $field->getHidden()];
		}

		return [];
	}
}
