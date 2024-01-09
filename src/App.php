<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Chuck\Error\ErrorRenderer;
use Conia\Core\Factory;
use Conia\Core\Middleware\InitRequest;
use Conia\Error\Handler;
use Conia\Quma\Connection;
use Conia\Quma\Database;
use Conia\Registry\Entry;
use Conia\Registry\Registry;
use Conia\Route\AddsRoutes;
use Conia\Route\Dispatcher;
use Conia\Route\Group;
use Conia\Route\Route;
use Conia\Route\RouteAdder;
use Conia\Route\Router;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Log\LoggerInterface as Logger;

/** @psalm-api */
readonly class App implements RouteAdder
{
    use AddsRoutes;

    protected readonly Dispatcher $dispatcher;
    protected readonly Database $db;

    public function __construct(
        protected readonly Config $config,
        protected readonly Factory $factory,
        protected readonly Router $router,
        protected readonly Registry $registry,
    ) {
        $this->dispatcher = new Dispatcher();
        $this->initializeRegistry();
    }

    public static function create(Config $config, Factory $factory): static
    {
        $app = new static($config, $factory, new Router(), new Registry());
        $app->middleware(new Handler($factory->responseFactory));
        $app->middleware(new InitRequest($config));

        return $app;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function registry(): Registry
    {
        return $this->registry;
    }

    /** @psalm-param Closure(Router $router):void $creator */
    public function routes(Closure $creator, string $cacheFile = '', bool $shouldCache = true): void
    {
        $this->router->routes($creator, $cacheFile, $shouldCache);
    }

    public function addRoute(Route $route): Route
    {
        return $this->router->addRoute($route);
    }

    public function addGroup(Group $group): void
    {
        $this->router->addGroup($group);
    }

    public function group(
        string $patternPrefix,
        Closure $createClosure,
        string $namePrefix = '',
    ): Group {
        $group = new Group($patternPrefix, $createClosure, $namePrefix);
        $this->router->addGroup($group);

        return $group;
    }

    public function staticRoute(
        string $prefix,
        string $path,
        string $name = '',
    ): void {
        $this->router->addStatic($prefix, $path, $name);
    }

    public function middleware(Middleware ...$middleware): void
    {
        $this->dispatcher->middleware(...$middleware);
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param non-empty-string $class
     */
    public function renderer(string $name, string $class): Entry
    {
        return $this->registry->tag(Renderer::class)->add($name, $class);
    }

    /**
     * @psalm-param non-empty-string $contentType
     * @psalm-param non-empty-string $renderer
     */
    public function errorRenderer(string $contentType, string $renderer, mixed ...$args): Entry
    {
        return $this->registry->tag(Handler::class)
            ->add($contentType, ErrorRenderer::class)->args(renderer: $renderer, args: $args);
    }

    public function logger(callable $callback): void
    {
        $this->registry->add(Logger::class, Closure::fromCallable($callback));
    }

    /**
     * @psalm-param non-empty-string $key
     * @psalm-param class-string|object $value
     */
    public function register(string $key, object|string $value): Entry
    {
        return $this->registry->add($key, $value);
    }

    public function initializeRegistry(): void
    {
        $this->registry->add(Router::class, $this->router);
        $this->registry->add($this->router::class, $this->router);

        $this->registry->add(Factory::class, $this->factory);
        $this->registry->add($this->factory::class, $this->factory);
    }

    public function section(string $name): void
    {
        $this->registry
            ->tag(Collection::class)
            ->add($name, new Section($name));
    }

    public function collection(string $class): void
    {
        $this->registry
            ->tag(Collection::class)
            ->add($class::handle(), $class);
    }

    public function node(string $class): void
    {
        $this->registry
            ->tag(Node::class)
            ->add($class::handle(), $class);
    }

    public function database(
        string $dsn,
        string|array $sql = null,
        string|array $migrations = null,
        array $options = [],
        bool $print = false
    ): void {
        $root = dirname(__DIR__);
        $sql = array_merge(
            [$root . '/db/sql'],
            $sql ? (is_array($sql) ? $sql : [$sql]) : []
        );
        $migrations = array_merge(
            [$root . '/db/migrations'],
            $migrations ? (is_array($migrations) ? $migrations : [$migrations]) : []
        );

        $this->db = new Database(new Connection(
            $dsn,
            $sql,
            $migrations,
            fetchMode: PDO::FETCH_ASSOC,
            options: $options,
            print: $print,
        ));
        $this->registry->add(Database::class, $this->db);
    }

    public function run(bool $sessionEnabled): Response|false
    {
        $request = $this->factory->serverRequest();
        $route = $this->router->match($request);
        $response = $this->dispatcher->dispatch($request, $route);

        // Add the system routes as last step
        (new Routes($this->config, $this->db, $this->factory, $sessionEnabled))->add($this);

        return (new Emitter())->emit($response) ? $response : false;
    }
}
