<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait IsHideable
{
	protected bool $hidden = false;

	public function hidden(bool $hidden = true): static
	{
		$this->hidden = $hidden;

		return $this;
	}
}
