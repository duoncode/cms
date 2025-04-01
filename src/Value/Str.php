<?php

declare(strict_types=1);

namespace Duon\Cms\Value;

use function Duon\Cms\Util\escape;

class Str extends Value
{
	public function __toString(): string
	{
		return escape($this->unwrap());
	}

	public function unwrap(): string
	{
		return $this->data['value'] ?? '';
	}

	public function json(): string
	{
		return $this->unwrap();
	}

	public function isset(): bool
	{
		return $this->unwrap() ? true : false;
	}
}
