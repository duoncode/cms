<?php

declare(strict_types=1);

namespace Conia;

use Chuck\App as ChuckApp;
use Chuck\RequestInterface;
use Chuck\ResponseInterface;
use Chuck\Registry;
use Chuck\RegistryInterface;
use Chuck\SessionInterface;
use Chuck\Routing\GroupInterface;
use Chuck\Routing\RouteInterface;
use Conia\Config;
use Conia\Request;
use Conia\Response;
use Conia\Routes;
use Conia\Session;


class App
{
    protected Routes $routes;
    protected ChuckApp $app;
    protected Config $config;

    public function __construct(
        array|Config $options,
        RegistryInterface $registry = new Registry(),
    ) {
        if ($options instanceof Config) {
            $config = $options;
        } else {
            $config = new Config($options);
        }

        $registry->add(SessionInterface::class, Session::class);
        $registry->add(RequestInterface::class, Request::class);
        $registry->add(ResponseInterface::class, Response::class);

        $this->config = $config;
        $this->app = ChuckApp::create($config, $registry);
    }

    public function addSystemRoutes(): void
    {
        (new Routes($this->config))->add($this);
    }

    public function addLayouts(array $layouts): void
    {
        $this->config->addLayouts($layouts);
    }

    public function add(RouteInterface $route): void
    {
        $this->app->add($route);
    }

    public function group(GroupInterface $group): void
    {
        $this->app->group($group);
    }

    public function static(
        string $name,
        string $prefix,
        string $path,
    ): void {
        $this->app->static($name, $prefix, $path);
    }

    public function middleware(callable ...$middlewares): void
    {
        $this->app->middleware(...$middlewares);
    }

    public function run(): ResponseInterface
    {
        return $this->app->run();
    }
}
