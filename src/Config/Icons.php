<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Icons
{
	/** @param list<non-empty-string> $localPaths */
	public function __construct(
		public array $localPaths,
		public Iconify $iconify,
	) {}
}
