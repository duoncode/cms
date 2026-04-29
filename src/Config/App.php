<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class App
{
	/**
	 * @param non-empty-string $name
	 * @param ?non-empty-string $secret
	 */
	public function __construct(
		public string $name,
		public bool $debug,
		public string $env,
		#[\SensitiveParameter]
		public ?string $secret,
	) {}
}
