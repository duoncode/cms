<?php

declare(strict_types=1);

namespace Duon\Cms\Boiler\Error;

use Duon\Cms\Cms;
use Duon\Cms\Config;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Locale;
use Duon\Cms\Locales;
use Duon\Cms\Node\Node;
use Duon\Core\Exception\HttpError;
use Duon\Core\Factory\Factory;
use Duon\Core\Request;
use Duon\Error\Handler as ErrorHandler;
use Duon\Error\Renderer as ErrorRenderer;
use Psr\Log\LoggerInterface as Logger;

/** @psalm-api */
final class Handler
{
	/** @var string|list<string>|null */
	private string|array|null $views = null;

	/** @var list<class-string> */
	private array $trusted = [
		Node::class,
		Cms::class,
		Locales::class,
		Locale::class,
		Config::class,
		Request::class,
	];

	public function __construct(
		private Config $config,
		private Factory $factory,
		private Logger $logger,
	) {}

	/** @param string|list<string> $views */
	public function views(string|array $views): self
	{
		$this->views = $views;

		return $this;
	}

	/** @param list<class-string> $trusted */
	public function trusted(array $trusted, bool $replace = false): self
	{
		if ($replace) {
			$this->trusted = $trusted;
		} else {
			$this->trusted = array_merge($this->trusted, $trusted);
		}

		return $this;
	}

	public function create(): ErrorHandler
	{
		$debug = $this->config->debug();
		$handler = new ErrorHandler($this->factory->responseFactory(), $debug);
		$handler->logger($this->logger);

		$renderer = $this->customRenderer();

		if ($renderer) {
			$handler->renderer($renderer, HttpError::class);
			$handler->renderer($renderer);
		} else {
			$rendererFactory = new RendererFactory(
				dirs: $this->errorViews(),
				autoescape: true,
				context: [
					'debug' => $debug,
					'env' => $this->config->env(),
				],
				trusted: $this->trustedClasses(),
			);
			$handler->renderer($rendererFactory->withTemplate('http-error'), HttpError::class);
			$handler->renderer($rendererFactory->withTemplate('http-server-error'));
		}

		if ($debug && $this->config->get('error.whoops') && WhoopsHandler::available()) {
			$handler->debugHandler(new WhoopsHandler());
		}

		return $handler;
	}

	private function customRenderer(): ?ErrorRenderer
	{
		$renderer = $this->config->get('error.renderer', null);

		if ($renderer === null) {
			return null;
		}

		if (is_string($renderer)) {
			if (!is_a($renderer, ErrorRenderer::class, true)) {
				throw new RuntimeException('Error renderer must implement ' . ErrorRenderer::class);
			}

			$renderer = new $renderer();
		}

		if ($renderer instanceof ErrorRenderer) {
			return $renderer;
		}

		throw new RuntimeException('Error renderer must implement ' . ErrorRenderer::class);
	}

	/** @return non-empty-list<string> */
	private function errorViews(): array
	{
		$views = $this->views ?? $this->config->get('error.views', null) ?? $this->projectViewPath();
		$dirs = [];

		foreach ((array) $views as $view) {
			if (!is_string($view) || $view === '') {
				continue;
			}

			$path = $this->resolvePath($view);

			if (is_dir($path)) {
				$dirs[] = $path;
			}
		}

		$dirs[] = $this->builtinViewPath();

		return array_values(array_unique($dirs));
	}

	private function projectViewPath(): string
	{
		$views = (string) $this->config->get('path.views');

		return $this->resolvePath($views);
	}

	private function resolvePath(string $path): string
	{
		if (str_starts_with($path, '/') && is_dir($path)) {
			return $path;
		}

		$root = (string) $this->config->get('path.root', getcwd() ?: '.');

		return rtrim($root, '/') . '/' . ltrim($path, '/');
	}

	private function builtinViewPath(): string
	{
		return dirname(__DIR__, 3) . '/resources/error';
	}

	/** @return list<class-string> */
	private function trustedClasses(): array
	{
		$trusted = $this->config->get('error.trusted', []);

		if (is_array($trusted)) {
			return array_values(array_unique(array_merge($this->trusted, $trusted)));
		}

		return $this->trusted;
	}
}
