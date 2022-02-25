<?php

declare(strict_types=1);


namespace Conia;


use Chuck\App as BaseApp;
use Chuck\Response;
use Chuck\ConfigInterface;
use Conia\Config;
use Conia\Routes;


class App extends BaseApp
{
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
        Routes::addCatchall($this);

        return parent::run();
    }
}
