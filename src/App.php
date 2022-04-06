<?php

declare(strict_types=1);


namespace Conia;


use Chuck\App as BaseApp;
use Chuck\RequestInterface;
use Chuck\ConfigInterface;
use Conia\Config;
use Conia\Routes;


class App extends BaseApp
{
    protected Routes $routes;

    public function __construct(RequestInterface $request)
    {
        parent::__construct($request);

        $this->routes = new Routes($request->getConfig());
    }

    public static function create(array|ConfigInterface $options): static
    {
        if ($options instanceof ConfigInterface) {
            $config = $options;
        } else {
            $config = new Config($options);
        }

        return parent::create($config);
    }

    public function addSystemRoutes(): void
    {
        $this->routes->add($this);
    }
}
