<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Value;

use function FiveOrbs\Cms\Util\escape;

class Option extends Value
{
	public function __toString(): string
	{
		return escape($this->unwrap());
	}

	public function unwrap(): string
	{
		return $this->data['value'];
	}

	public function json(): array
	{
		return $this->data;
	}

	public function isset(): bool
	{
		return isset($this->data['value']);
	}
}
