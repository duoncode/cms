<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Iconify
{
	/**
	 * @param non-empty-string $baseUrl
	 * @param positive-int $timeout
	 * @param non-empty-string $userAgent
	 */
	public function __construct(
		public string $baseUrl,
		public int $timeout,
		public string $userAgent,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('icons.iconify.base_url'),
			$config->get('icons.iconify.timeout'),
			$config->get('icons.iconify.user_agent'),
		);
	}
}
