<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Resizable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Rows implements Capability
{
	public function __construct(public readonly int $rows) {}

	public function set(Field $field): void
	{
		if ($field instanceof Resizable) {
			$field->rows($this->rows);
			return;
		}

		$cap = Resizable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
