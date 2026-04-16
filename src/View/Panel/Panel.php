<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

use Duon\Cms\Config;
use Duon\Cms\Locales;
use Duon\Cms\Node\Types;
use Duon\Container\Container;
use Duon\Core\Request;

class Panel
{
	protected string $publicPath;

	public function __construct(
		protected readonly Request $request,
		protected readonly Config $config,
		protected readonly Container $container,
		protected readonly Locales $locales,
		protected readonly Types $types,
	) {
		$this->publicPath = $config->get('path.public');
	}

	public function index(): array
	{
		return [];
	}
}
