<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value;

class Video extends Field implements Capability\Translatable, Capability\FileTranslatable
{
	use Capability\IsTranslatable;
	use Capability\FileIsTranslatable;

	public function value(): Value\Video
	{
		if ($this->translateFile) {
			return new Value\Video($this->node, $this, $this->valueContext);
		}

		return new Value\Video($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		if ($this->translateFile) {
			return $this->getTranslatableFileStructure('video', $value);
		}

		return $this->getFileStructure('video', $value);
	}
}
