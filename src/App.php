<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Chuck\Error\Handler;
use Conia\Chuck\Middleware;
use Conia\Chuck\Registry;
use Conia\Chuck\Router;
use Conia\Core\Config;
use Conia\Core\Node\Node;
use Conia\Core\Routes;
use Conia\Quma\Connection;
use PDO;
use Psr\Container\ContainerInterface as PsrContainer;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Server\MiddlewareInterface as PsrMiddleware;

class App extends \Conia\Chuck\App
{
    /** @psalm-param non-falsy-string|list{non-falsy-string, ...}|Closure|Middleware|PsrMiddleware|null $errorHandler */
    public function __construct(
        protected Config $config,
        protected Router $router,
        protected Registry $registry,
        protected string|array|Closure|Middleware|PsrMiddleware|null $errorHandler = null,
    ) {
        $registry->add(Config::class, $config);
        parent::__construct($router, $registry, $errorHandler);
    }

    public static function fromConfig(Config $config, ?PsrContainer $container = null): static
    {
        $registry = new Registry($container);
        $router = new Router();

        return new static($config, $router, $registry, Handler::class);
    }

    public static function create(?PsrContainer $container = null): static
    {
        $config = new Config('conia', debug: false);

        return static::fromConfig($config, $container);
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

        $this->registry->add(Connection::class, new Connection(
            $dsn,
            $sql,
            $migrations,
            fetchMode: PDO::FETCH_ASSOC,
            options: $options,
            print: $print,
        ));
    }

    public function run(): PsrResponse
    {
        // Add the system routes as last step
        (new Routes($this->config))->add($this);

        return parent::run();
    }
}
