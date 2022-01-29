<?php

declare(strict_types=1);


namespace Conia;


use Chuck\App as BaseApp;
use Conia\Config;
use Conia\Routes;


class App extends BaseApp
{
    public function __construct(Config $config)
    {
        Routes::add($this);
    }

    public run(): void
    {
        Routes::addCatchall($this);

        parent::run()
    }
}
