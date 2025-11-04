<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Translatable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Translate implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Translatable) {
			$field->translate(true);
			return;
		}

		$cap = Translatable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
