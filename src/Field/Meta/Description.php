<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Describable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

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

		throw new RuntimeException(capabilityErrorMessage($field, Describable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Describable) {
			return ['description' => $field->getDescription()];
		}

		return [];
	}
}
