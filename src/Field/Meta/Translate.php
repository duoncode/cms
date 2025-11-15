<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Translatable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Translate implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof Translatable) {
			$field->translate(true);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, Translatable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Translatable) {
			return ['translate' => $field->isTranslatable()];
		}

		return [];
	}
}
