<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

use Duon\Cms\Config;
use Duon\Cms\Navigation;
use Duon\Container\Container;
use Duon\Core\Request;

abstract class Panel
{
	protected string $panelDir;

	public function __construct(
		protected Config $config,
		protected Container $container,
		protected readonly Request $request,
	) {
		$this->panelDir = __DIR__ . '/../../../panel';
	}

	protected function context(array $data = []): array
	{
		$panelPath = $this->config->get('path.panel');

		return array_merge([
			'debug' => $this->config->debug,
			'env' => $this->config->env,
			'boosted' => $this->request->hasHeader('HX-Boosted'),
			'htmx' => $this->request->hasHeader('HX-Request'),
			'panelPath' => $this->config->get('path.panel'),
			'config' => $this->config,
			'stylesheets' => $this->stylesheets($panelPath),
			'scripts' => $this->scripts($panelPath),
			'collections' => $this->collections(),
		], $data);
	}

	private function stylesheets(string $panelPath): array
	{
		$theme = $this->config->get('panel.theme', null);
		return array_merge(
			$theme ? [$theme] : [],
			[
				"{$panelPath}/assets/styles/tokens.css",
				"{$panelPath}/assets/styles/app.css",
			],
		);
	}

	private function scripts(string $panelPath): array
	{
		return [
			"{$panelPath}/assets/app/vendor/htmx.js",
			"{$panelPath}/assets/app/panel.js",
		];
	}

	protected function collections(): array
	{
		/** @var Navigation $navigation */
		$navigation = $this->container->get(Navigation::class);

		return $navigation->items();
	}
}
