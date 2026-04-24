<?php

declare(strict_types=1);

namespace Duon\Cms\Contract;

interface Icons
{
	/** @param array<array-key, mixed> $args */
	public function icon(string $id, array $args = []): string;
}
