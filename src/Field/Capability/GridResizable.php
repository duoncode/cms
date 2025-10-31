<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

interface GridResizable
{
	public function columns(int $columns, int $minCellWidth = 1): static;
}
