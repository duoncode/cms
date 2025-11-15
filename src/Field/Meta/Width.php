<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Meta;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Resizable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Meta\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Width implements Capability
{
	public function __construct(public readonly int $width) {}

	public function set(Field $field): void
	{
		if ($field instanceof Resizable) {
			$field->width($this->width);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, Resizable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Resizable) {
			return ['width' => $field->getWidth()];
		}

		return [];
	}
}
