<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Panel
{
	/**
	 * @param non-empty-string $path
	 * @param list<non-empty-string> $theme
	 * @param ?non-empty-string $logo
	 */
	public function __construct(
		public string $path,
		public array $theme,
		public ?string $logo,
	) {}
}
