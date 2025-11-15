<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Searchable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

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

		throw new RuntimeException(capabilityErrorMessage($field, Searchable::class));
	}

	public function properties(Field $field): array
	{
		// Fulltext is not serialized to frontend - it's used for search functionality only
		return [];
	}
}
