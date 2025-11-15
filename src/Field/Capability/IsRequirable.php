<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait IsRequirable
{
	public function required(): static
	{
		$this->validators[] = 'required';

		return $this;
	}

	public function isRequired(): bool
	{
		return in_array('required', $this->validators);
	}
}
