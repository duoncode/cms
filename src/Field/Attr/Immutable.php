<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Immutable as ImmutableCapability;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Attr\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Immutable implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof ImmutableCapability) {
			$field->immutable(true);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, ImmutableCapability::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof ImmutableCapability) {
			return ['immutable' => $field->getImmutable()];
		}

		return [];
	}
} // We can't use Readonly as it is a keyword of PHP
