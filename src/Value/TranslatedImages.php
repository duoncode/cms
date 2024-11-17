<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Value;

class TranslatedImages extends Images
{
	public function current(): TranslatedImage
	{
		return $this->get($this->pointer);
	}

	protected function get(int $index): TranslatedImage
	{
		return new TranslatedImage($this->node, $this->field, $this->context, $index);
	}
}
