<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Value;

class Images extends Files
{
	public function __toString(): string
	{
		$out = '';

		for ($i = 0; $i < count($this->data['files']); $i++) {
			$out .= (string) $this->get($i);
		}

		return $out;
	}

	public function current(): Image
	{
		return $this->get($this->pointer);
	}

	public function get(int $index): Image
	{
		return new Image($this->node, $this->field, $this->context, $index);
	}

	public function first(): Image
	{
		return new Image($this->node, $this->field, $this->context, 0);
	}
}
