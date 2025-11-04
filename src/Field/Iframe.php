<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Field;
use Duon\Cms\Value\Youtube as YoutubeValue;

class Iframe extends Field implements Capability\Translatable
{
	use Capability\IsTranslatable;

	public function value(): YoutubeValue
	{
		return new YoutubeValue($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		return array_merge($this->getSimpleStructure('iframe', $value), [
			'iframeWidth' => '100%',
			'iframeHeight' => '75%',
		]);
	}
}
