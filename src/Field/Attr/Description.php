<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Describable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Description implements Capability
{
	public function __construct(public readonly string $description) {}

	public function set(Field $field): void
	{
		if ($field instanceof Describable) {
			$field->description($this->description);
			return;
		}

		$cap = Describable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
