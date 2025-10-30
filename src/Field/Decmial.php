<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Decimal as DecimalValue;

class Decimal extends Field
{
	public const EXTRA_CAPABILITIES = Field::CAPABILITY_HIDDEN;

	public function value(): DecimalValue
	{
		return new DecimalValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('decimal', $value);
	}
}
