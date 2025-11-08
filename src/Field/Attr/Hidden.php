<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Hidable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Hidden implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Hidable) {
			$field->hidden(true);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . Hidable::class);
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Hidable) {
			return ['hidden' => $field->getHidden()];
		}

		return [];
	}
}
