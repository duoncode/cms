<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validate extends Capability
{
	public readonly array $validators;

	public function __construct(string ...$validators)
	{
		$this->validators = $validators;
	}

	public function capabilities(): int
	{
		return Field::CAPABILITY_VALIDATE;
	}
}
