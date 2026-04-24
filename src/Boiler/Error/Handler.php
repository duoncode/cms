<?php

declare(strict_types=1);

namespace Duon\Cms\Boiler\Error;

use Duon\Cms\Cms;
use Duon\Cms\Config;
use Duon\Cms\Locale;
use Duon\Cms\Locales;
use Duon\Cms\Node\Node;
use Duon\Core\Exception\HttpError;
use Duon\Core\Factory;
use Duon\Core\Request;
use Duon\Error\Handler as ErrorHandler;
use Psr\Log\LoggerInterface as Logger;

use function Duon\Core\env;

/** @psalm-api */
final class Handler
{
	/** @var non-empty-string */
	private string $views;

	/** @var list<class-string> */
	private array $trusted = [
		Node::class,
		Cms::class,
		Locales::class,
		Locale::class,
		Config::class,
		Request::class,
	];

	/** @param non-empty-string $root */
	public function __construct(
		private string $root,
		private Logger $logger,
		private Factory $factory,
	) {
		$this->views = "{$this->root}/views";
	}

	/** @param non-empty-string $views */
	public function views(string $views): self
	{
		$this->views = "{$this->root}/{$views}";

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
		$rendererFactory = new RendererFactory(
			dirs: $this->views,
			autoescape: true,
			context: [
				'debug' => env('CMS_DEBUG'),
				'env' => env('CMS_ENV'),
			],
			trusted: $this->trusted,
		);
		$handler = new ErrorHandler($this->factory->responseFactory(), (bool) env('CMS_DEBUG'));
		$handler->logger($this->logger);
		$handler->renderer($rendererFactory->withTemplate('http-error'), HttpError::class);
		$handler->renderer($rendererFactory->withTemplate('http-server-error'));

		if (env('CMS_DEBUG')) {
			$handler->debugHandler(new WhoopsHandler());
		}

		return $handler;
	}
}
