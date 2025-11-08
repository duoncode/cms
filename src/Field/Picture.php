<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value;

class Picture extends Field
{
	use Capability\DoesAllowMultiple;
	use Capability\IsTranslatable;
	use Capability\FileIsTranslatable;

	// TODO: translateFile and multiple
	public function value(): Value\Picture
	{
		if ($this->translateFile) {
			return new Value\TranslatedPicture($this->node, $this, $this->valueContext);
		}

		return new Value\Picture($this->node, $this, $this->valueContext);
	}

	public function properties(): array
	{
		$value = $this->value();
		$count = $value->count();

		// Generate thumbs
		// TODO: add it to the api data. Currently we assume in the frontend that they are existing
		for ($i = 0; $i < $count; $i++) {
			$value->width(400)->url(false, $i);
		}

		return parent::properties();
	}

	public function structure(mixed $value = null): array
	{
		return $this->getFileStructure('picture', $value);
	}
}
