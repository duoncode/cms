<?php

declare(strict_types=1);

namespace Duon\Cms\Value;

use function Duon\Cms\Util\escape;

class Iframe extends Value
{
	protected string $value;

	public function __toString(): string
	{
		return escape($this->unwrap());
	}

	public function unwrap(): string
	{
		if (isset($this->value)) {
			return $this->value;
		}

		if ($this->translate) {
			$locale = $this->locale;

			while ($locale) {
				$value = $this->data['value'][$locale->id] ?? null;

				if ($value) {
					$this->value = $value;

					return $value;
				}

				$locale = $locale->fallback();
			}

			$this->value = '';

			return '';
		}

		$this->value = isset($this->data['value']) ?
			$this->data['value'] : '';

		return $this->value;
	}

	public function json(): mixed
	{
		return $this->unwrap();
	}

	public function isset(): bool
	{
		return $this->unwrap() ?? null ? true : false;
	}
}
