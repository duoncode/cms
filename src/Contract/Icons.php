<?php

declare(strict_types=1);

namespace Duon\Cms\Contract;

interface Icons
{
	public function icon(
		string $id,
		array $args = [],
	): string;
}
