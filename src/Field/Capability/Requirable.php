<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Requirable
{
	public function required(): static;

	public function isRequired(): bool;
}
