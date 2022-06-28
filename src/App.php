<?php

declare(strict_types=1);

namespace Conia;

use \RuntimeException;
use Chuck\App as BaseApp;
use Chuck\ConfigInterface;
use Chuck\Response\ResponseInterface;
use Chuck\Routing\RouterInterface;
use Chuck\Routing\Router;
use Chuck\Error\Handler;
use Conia\Config;
use Conia\Request;
use Conia\Routes;


class App extends BaseApp
{
    public function __construct(
        private Request $request,
        private Config $config,
        private RouterInterface $router,
    ) {
        parent::__construct($request, $config, $router);
    }

    public static function create(
        ConfigInterface $config,
    ): static {
        throw new RuntimeException('Use \Conia\App::new');
    }

    public static function new(
        Config $config,
    ): static {
        $router = new Router();
        $request = new Request($config, $router, new Session($config->app()));

        Model::init($request);

        $errorHandler = new Handler($request);
        $errorHandler->setup();

        $app = new self($request, $config, $router);

        return $app;
    }

    public function type(Type $type): void
    {
        $this->config->addType($type);
    }

    public function middleware(callable ...$middlewares): void
    {
        $this->app->middleware(...$middlewares);
    }

    public function run(): ResponseInterface
    {
        // Add the system routes as last step
        (new Routes($this->config))->add($this);

        return parent::run();
    }
}
