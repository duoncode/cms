<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Path
{
	/**
	 * @param non-empty-string $root
	 * @param non-empty-string $public
	 * @param non-empty-string $assets
	 * @param non-empty-string $cache
	 * @param non-empty-string $views
	 * @param non-empty-string $panel
	 * @param ?non-empty-string $api
	 */
	public function __construct(
		public string $root,
		public string $public,
		public string $prefix,
		public string $assets,
		public string $cache,
		public string $views,
		public string $panel,
		public ?string $api,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('path.root'),
			$config->get('path.public'),
			$config->get('path.prefix'),
			$config->get('path.assets'),
			$config->get('path.cache'),
			$config->get('path.views'),
			$config->get('path.panel'),
			$config->get('path.api'),
		);
	}
}
