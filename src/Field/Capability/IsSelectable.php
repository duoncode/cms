<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait IsSelectable
{
	protected array $options = [];

	public function add(string|array $option): void
	{
		$this->options[] = $option;
	}

	public function options(array $options): void
	{
		if (is_array($options[0])) {
			$this->hasLabel = true;
		}

		$this->options = $options;
	}
}
