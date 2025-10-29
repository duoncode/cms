<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Value\Html as HtmlValue;

class Html extends Field
{
	public const EXTRA_CAPABILITIES = Field::CAPABILITY_TRANSLATE;

	public function value(): HtmlValue
	{
		return new HtmlValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getTranslatableStructure('html', $value);
	}
}
