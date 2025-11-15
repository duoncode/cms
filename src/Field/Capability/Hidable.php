<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Hidable
{
	public function hidden(bool $hidden = true): static;
}
