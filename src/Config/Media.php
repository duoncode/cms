<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Media
{
	/** @param null|'apache'|'nginx' $fileServer */
	public function __construct(
		public ?string $fileServer,
	) {}
}
