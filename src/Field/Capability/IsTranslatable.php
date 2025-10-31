<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait IsTranslatable
{
	protected bool $translate = false;

	public function translate(bool $translate = true): static
	{
		$this->translate = $translate;

		return $this;
	}

	public function isTranslatable(): bool
	{
		return $this->translate;
	}
}
