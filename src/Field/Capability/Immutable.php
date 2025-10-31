<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Immutable
{
	public function immutable(bool $immutable = true): static;
}
