<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

use Duon\Cms\Collection;
use Duon\Cms\Config;
use Duon\Cms\Section;
use Duon\Container\Container;
use Duon\Core\Request;
use Duon\Wire\Creator;

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
			'collections' => $this->collections(),
		];
	}

	protected function collections(): array
	{
		$creator = new Creator($this->container);
		$tag = $this->container->tag(Collection::class);
		$collections = [];

		foreach ($tag->entries() as $id) {
			$class = $tag->entry($id)->definition();

			if (is_object($class)) {
				$item = $class;
			} else {
				$item = $creator->create($class, predefinedTypes: [Request::class => $this->request]);
			}

			$collections[] = $item;
		}

		return $collections;
	}
}
