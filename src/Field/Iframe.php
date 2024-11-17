<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Field;

use FiveOrbs\Cms\Field\Field;
use FiveOrbs\Cms\Value\Youtube as YoutubeValue;

class Iframe extends Field
{
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
