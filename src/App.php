<?php

declare(strict_types=1);


namespace Conia;


use Chuck\App as BaseApp;
use Chuck\Response;
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

        $this->routes = new Routes($this, $request->getConfig());
    }

    public static function create(array|ConfigInterface $options): self
    {
        if ($options instanceof ConfigInterface) {
            $config = $options;
        } else {
            $config = new Config($options);
        }

        $app = parent::create($config);

        return $app;
    }

    public function run(): Response
    {
        // Register the global catchall as last step
        $this->routes->addCatchall($this);

        return parent::run();
    }
}
