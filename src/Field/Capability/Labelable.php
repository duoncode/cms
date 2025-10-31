<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Labelable
{
	public function label(string $label);

	public function getLabel(): ?string;
}
