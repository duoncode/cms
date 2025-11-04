<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Value\Text as TextValue;

class Text extends Field implements Capability\Translatable
{
	use Capability\IsTranslatable;

	public function value(): TextValue
	{
		return new TextValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getTranslatableStructure('text', $value);
	}
}
