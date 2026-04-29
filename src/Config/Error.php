<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

use Duon\Error\Renderer;

final readonly class Error
{
	/**
	 * @param null|class-string<Renderer>|Renderer $renderer
	 * @param list<class-string> $trusted
	 * @param null|non-empty-string|list<non-empty-string> $views
	 */
	public function __construct(
		public bool $enabled,
		public string|Renderer|null $renderer,
		public array $trusted,
		public string|array|null $views,
		public bool $whoops,
	) {}
}
