<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

use Duon\Cms\Config;

abstract class Panel
{
	protected string $panelDir;

	public function __construct(
		protected Config $config,
	) {
		$this->panelDir = __DIR__ . '/../../../panel';
	}

	protected function context(): array
	{
		return [
			'debug' => $this->config->debug,
			'env' => $this->config->env,
			'panelPath' => $this->config->get('path.panel'),
			'config' => $this->config,
			'cssFiles' => [
				'tokens.css',
				'app.css',
			],
			'jsFiles' => [
				'panel.js',
			],
		];
	}
}
