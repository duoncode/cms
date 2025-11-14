<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Capability\Selectable;
use Duon\Cms\Field\Field;

use function Duon\Cms\Field\Attr\capabilityErrorMessage;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Options implements Capability
{
	public function __construct(public readonly array $options) {}

	public function set(Field $field): void
	{
		if ($field instanceof Selectable) {
			$field->options($this->options);

			return;
		}

		throw new RuntimeException(capabilityErrorMessage($field, Selectable::class));
	}

	public function properties(Field $field): array
	{
		if ($field instanceof Selectable) {
			return ['options' => $field->getOptions()];
		}

		return [];
	}
}
