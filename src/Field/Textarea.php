<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

class Textarea extends Text
{
	public const EXTRA_CAPABILITIES = Field::CAPABILITY_TRANSLATE;

	public function structure(mixed $value = null): array
	{
		return $this->getTranslatableStructure('textarea', $value);
	}
}
