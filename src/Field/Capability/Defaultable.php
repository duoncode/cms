<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Defaultable
{
	public function default(mixed $default): static;
}
