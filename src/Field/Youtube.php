<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Youtube as YoutubeValue;

class Youtube extends Field
{
	public function value(): YoutubeValue
	{
		return new YoutubeValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return $this->getSimpleStructure('youtube', $value);
	}
}
