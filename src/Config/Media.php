<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Media
{
	/** @param null|'apache'|'nginx' $fileServer */
	public function __construct(
		public ?string $fileServer,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('media.fileserver'),
		);
	}
}
