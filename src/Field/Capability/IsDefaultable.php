<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait IsDefaultable
{
	protected mixed $default = null;

	public function default(mixed $default): static
	{
		$this->default = $default;

		return $this;
	}
}
