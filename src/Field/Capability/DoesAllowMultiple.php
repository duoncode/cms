<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait DoesAllowMultiple
{
	protected bool $multiple = false;

	public function multiple(bool $multiple = true): static
	{
		$this->multiple = $multiple;

		return $this;
	}
}
