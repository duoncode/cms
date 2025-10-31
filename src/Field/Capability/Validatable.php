<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Validatable
{
	public function validate(string ...$validators): static;

	public function validators(): array;
}
