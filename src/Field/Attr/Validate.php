<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\Validatable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validate implements Capability
{
	public readonly array $validators;

	public function __construct(string ...$validators)
	{
		$this->validators = $validators;
	}

	public function set(Field $field): void
	{
		if ($field instanceof Validatable) {
			$field->validate(...$this->validators);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . Validatable::class);
	}
}
