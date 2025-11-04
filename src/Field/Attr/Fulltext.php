<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Searchable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Fulltext implements Capability
{
	public function __construct(public readonly FulltextWeight $fulltextWeight) {}

	public function set(Field $field): void
	{
		if ($field instanceof Searchable) {
			$field->fulltext($this->fulltextWeight);
			return;
		}

		$cap = Searchable::class;
		throw new RuntimeException("The field {$field::class} does not have the capability {$cap}");
	}
}
