<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\AllowsMultiple;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Multiple implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof AllowsMultiple) {
			$field->multiple(true);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . AllowsMultiple::class);
	}
}
