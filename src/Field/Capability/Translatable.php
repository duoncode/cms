<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface Translatable
{
	public function translate(bool $translate = true): static;

	public function isTranslatable(): bool;
}
