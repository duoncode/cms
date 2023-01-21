<?php

declare(strict_types=1);

namespace Conia;

use Conia\Chuck\Config;
use Conia\Chuck\Error\Handler;
use Conia\Chuck\Middleware;
use Conia\Chuck\Registry;
use Conia\Chuck\Router;
use Conia\Quma\Connection;
use Conia\Routes;
use PDO;
use Psr\Container\ContainerInterface as PsrContainer;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Server\MiddlewareInterface as PsrMiddleware;

class App extends \Conia\Chuck\App
{
    public function __construct(
        protected Config $config,
        protected Router $router,
        protected Registry $registry,
        protected Middleware|PsrMiddleware|null $errorHandler = null,
    ) {
        parent::__construct($config, $router, $registry, $errorHandler);
    }

    public static function create(?Config $config = null, ?PsrContainer $container = null): self
    {
        if (!$config) {
            $config = new Config('conia', debug: false);
        }

        $registry = new Registry($container);
        $router = new Router();
        $errorHandler = new Handler($config, $registry);
        // The error handler should be the first middleware
        $router->middleware($errorHandler);

        return new self($config, $router, $registry, $errorHandler);
    }

    public function type(string $class, string $label = null, string $description = null): void
    {
        $this->registry
            ->tag(Type::class)
            ->add($class)
            ->args(label: $label, description: $description);
    }

    public function database(
        string $dsn,
        string|array $sql,
        string|array $migrations = null,
        array $options = [],
        bool $print = false
    ): void {
        $root = dirname(__DIR__);
        $sql = array_merge([$root . '/db/sql'], is_array($sql) ? $sql : [$sql]);
        $migrations = $migrations ? (is_array($migrations) ? $migrations : [$migrations]) : [];
        $migrations = array_merge([$root . '/db/migrations'], $migrations);

        $this->registry->add(Connetcion::class, new Connection(
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
