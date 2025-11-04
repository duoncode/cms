<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

class Textarea extends Text implements Capability\Translatable
{
	use Capability\IsTranslatable;

	public function structure(mixed $value = null): array
	{
		return $this->getTranslatableStructure('textarea', $value);
	}
}
