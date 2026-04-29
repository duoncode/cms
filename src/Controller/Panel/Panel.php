<?php

declare(strict_types=1);

namespace Duon\Cms\Controller\Panel;

use Duon\Cms\Config;
use Duon\Cms\Contract\Icons;
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
			'debug' => $this->config->debug(),
			'env' => $this->config->env(),
			'boosted' => $this->request->hasHeader('HX-Boosted'),
			'htmx' => $this->request->hasHeader('HX-Request'),
			'panelPath' => $this->config->get('path.panel'),
			'currentPath' => $this->request->uri()->getPath(),
			'logo' => $this->logo(),
			'config' => $this->config,
			'renderIcon' => $this->renderIcon(...),
			'stylesheets' => $this->stylesheets($panelPath),
			'scripts' => $this->scripts($panelPath),
			'collections' => $this->collections(),
		], $data);
	}

	private function stylesheets(string $panelPath): array
	{
		return array_merge(
			$this->config->get('panel.theme'),
			[
				"{$panelPath}/assets/styles/tokens.css",
				"{$panelPath}/assets/styles/reset.css",
				"{$panelPath}/assets/styles/app.css",
				"{$panelPath}/assets/styles/collection.css",
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

	private function logo(): ?string
	{
		$logo = $this->config->get('panel.logo', null);

		if ($logo === null) {
			return null;
		}

		$logo = trim((string) $logo);

		return $logo === '' ? null : $logo;
	}

	protected function collections(): array
	{
		/** @var Navigation $navigation */
		$navigation = $this->container->get(Navigation::class);

		return $navigation->items();
	}

	/** @param array{id: string, args?: array<array-key, mixed>}|null $icon */
	private function renderIcon(?array $icon): string
	{
		if ($icon === null) {
			return '';
		}

		$id = $icon['id'] ?? null;

		if (!is_string($id) || trim($id) === '') {
			return '';
		}

		$service = $this->container->get(Icons::class);

		if (!$service instanceof Icons) {
			return '';
		}

		$args = $icon['args'] ?? [];

		return $service->icon(trim($id), is_array($args) ? $args : []);
	}
}
