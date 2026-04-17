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
		$panelPath = $this->config->get('path.panel');
		$theme = $this->config->get('panel.theme');
		$cssFiles = array_merge(
			$theme ? [$theme] : [],
			[
				"{$panelPath}/assets/styles/tokens.css",
				"{$panelPath}/assets/styles/app.css",
			],
		);

		return [
			'debug' => $this->config->debug,
			'env' => $this->config->env,
			'panelPath' => $this->config->get('path.panel'),
			'config' => $this->config,
			'cssFiles' => $cssFiles,
			'jsFiles' => [
				'panel.js',
			],
		];
	}
}
