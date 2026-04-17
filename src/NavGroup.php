<?php

declare(strict_types=1);

namespace Duon\Cms;

interface NavGroup
{
	public function section(string $label): Section;

	public function collection(string $class): CollectionRef;
}
