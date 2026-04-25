<?php

declare(strict_types=1);

namespace Duon\Cms;

use BadMethodCallException;
use Closure;
use Duon\Cms\Boiler\Error\Handler as ErrorHandler;
use Duon\Cms\Node\Types;
use Duon\Container\Container;
use Duon\Container\Entry;
use Duon\Core\App as CoreApp;
use Duon\Core\Factory\Discovery;
use Duon\Core\Factory\Factory;
use Duon\Core\Plugin as CorePlugin;
use Duon\Router\After;
use Duon\Router\Before;
use Duon\Router\Endpoint;
use Duon\Router\Group;
use Duon\Router\Route;
use Duon\Router\RouteAdder;
use Duon\Router\Router;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface as Logger;

/**
 * Convenience facade for CMS applications.
 */
class App implements RouteAdder
{
	protected readonly CoreApp $core;
	protected readonly Plugin $plugin;
	protected bool $booted = false;

	public function __construct(
		protected readonly Config $config,
		Factory $factory,
		Router $router,
		Container $container,
	) {
		$this->core = new CoreApp($factory, $router, $container);
		$this->plugin = new Plugin($config);
		$this->addErrorHandler($container, $factory);
	}

	protected function addErrorHandler(Container $container, Factory $factory): void
	{
		if ($this->config->get('error.enabled')) {
			$this->core->middleware(
				new ErrorHandler($this->config, $factory, new ContainerLogger($container))->create(),
			);
		}
	}

	public static function create(Config $config): self
	{
		return new self(
			$config,
			Discovery::create(),
			new Router((string) $config->get('path.prefix')),
			new Container(),
		);
	}

	public function boot(): self
	{
		if (!$this->booted) {
			$this->core->load($this->plugin);
			$this->core->addRoute($this->plugin->catchallRoute());
			$this->booted = true;
		}

		return $this;
	}

	public function run(?Request $request = null): Response|false
	{
		$this->boot();

		return $this->core->run($request);
	}

	public function config(): Config
	{
		return $this->config;
	}

	public function core(): CoreApp
	{
		return $this->core;
	}

	public function plugin(): Plugin
	{
		return $this->plugin;
	}

	public function section(string $name): Section
	{
		return $this->plugin->section($name);
	}

	/** @param class-string<Collection> $class */
	public function collection(string $class): Collection
	{
		return $this->plugin->collection($class);
	}

	/** @param class-string<Contract\Icons>|Contract\Icons $icons */
	public function icons(string|Contract\Icons $icons, bool $replace = false): self
	{
		$this->plugin->icons($icons, $replace);

		return $this;
	}

	public function navigation(): Navigation
	{
		return $this->plugin->navigation();
	}

	public function meta(): Types
	{
		return $this->plugin->meta();
	}

	/** @param class-string $class */
	public function node(string $class): self
	{
		$this->plugin->node($class);

		return $this;
	}

	/** @param class-string<Renderer> $class */
	public function renderer(string $id, string $class): Entry
	{
		return $this->plugin->renderer($id, $class);
	}

	public function load(CorePlugin $plugin): self
	{
		$this->core->load($plugin);

		return $this;
	}

	/** @psalm-param Closure(Router $router):void $creator */
	public function routes(Closure $creator, string $cacheFile = '', bool $shouldCache = true): self
	{
		$this->core->routes($creator, $cacheFile, $shouldCache);

		return $this;
	}

	public function addRoute(Route $route): Route
	{
		return $this->core->addRoute($route);
	}

	public function addGroup(Group $group): void
	{
		$this->core->addGroup($group);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function route(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->route($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function get(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->get($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function post(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->post($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function put(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->put($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function patch(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->patch($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function delete(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->delete($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function head(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->head($pattern, $view, $name);
	}

	/** @psalm-param \Duon\Router\Route::View $view */
	public function options(string $pattern, callable|array|string $view, string $name = ''): Route
	{
		return $this->core->options($pattern, $view, $name);
	}

	/** @psalm-param class-string $controller */
	public function endpoint(array|string $path, string $controller, string|array $args): Endpoint
	{
		return $this->core->endpoint($path, $controller, $args);
	}

	public function group(
		string $patternPrefix,
		Closure $createClosure,
		string $namePrefix = '',
	): Group {
		return $this->core->group($patternPrefix, $createClosure, $namePrefix);
	}

	public function staticRoute(string $prefix, string $path, string $name = ''): self
	{
		$this->core->staticRoute($prefix, $path, $name);

		return $this;
	}

	public function before(Before $beforeHandler): self
	{
		$this->core->before($beforeHandler);

		return $this;
	}

	/** @return list<Before> */
	public function beforeHandlers(): array
	{
		return $this->core->beforeHandlers();
	}

	public function after(After $afterHandler): self
	{
		$this->core->after($afterHandler);

		return $this;
	}

	/** @return list<After> */
	public function afterHandlers(): array
	{
		return $this->core->afterHandlers();
	}

	/** @return list<Middleware> */
	public function getMiddleware(): array
	{
		return $this->core->getMiddleware();
	}

	public function middleware(Middleware ...$middleware): self
	{
		$this->core->middleware(...$middleware);

		return $this;
	}

	public function logger(Logger|callable $logger): self
	{
		$this->core->logger($logger);

		return $this;
	}

	public function container(): Container
	{
		return $this->core->container();
	}

	public function factory(): Factory
	{
		return $this->core->factory();
	}

	public function router(): Router
	{
		return $this->core->router();
	}

	/**
	 * @psalm-param non-empty-string $key
	 * @psalm-param class-string|object $value
	 */
	public function register(string $key, object|string $value): Entry
	{
		return $this->core->register($key, $value);
	}

	/** @param list<mixed> $args */
	public function __call(string $method, array $args): mixed
	{
		$coreCallable = is_callable([$this->core, $method]);
		$pluginCallable = is_callable([$this->plugin, $method]);

		if ($coreCallable && $pluginCallable) {
			throw new BadMethodCallException("Ambiguous CMS app method: {$method}");
		}

		if ($pluginCallable) {
			return $this->plugin->{$method}(...$args);
		}

		if ($coreCallable) {
			return $this->core->{$method}(...$args);
		}

		throw new BadMethodCallException("Unknown CMS app method: {$method}");
	}
}
