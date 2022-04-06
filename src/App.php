<?php

declare(strict_types=1);

namespace Conia;

use Chuck\App as BaseApp;
use Chuck\ConfigInterface;
use Chuck\RequestInterface;
use Chuck\ResponseInterface;
use Chuck\Registry;
use Chuck\RegistryInterface;
use Conia\Config;
use Conia\Request;
use Conia\Response;
use Conia\Routes;


class App extends BaseApp
{
    protected Routes $routes;

    public static function create(
        array|ConfigInterface $options,
        RegistryInterface $registry = new Registry(),
    ): static {
        if ($options instanceof ConfigInterface) {
            $config = $options;
        } else {
            $config = new Config($options);
        }

        $registry->add(RequestInterface::class, Request::class);
        $registry->add(ResponseInterface::class, Response::class);

        return parent::create($config, $registry);
    }

    public function addSystemRoutes(): void
    {
        (new Routes($this->request->getConfig()))->add($this);
    }
}
