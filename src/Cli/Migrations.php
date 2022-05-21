<?php

declare(strict_types=1);

namespace Conia\Cli;

use Chuck\App;
use Chuck\Cli\Migrations\Migrations as BaseMigrations;
use Conia\Config;


class Migrations extends BaseMigrations
{
    public function run(App $app): string|int
    {
        $config = $app->config();
        $result = parent::run($app);

        echo (print_r($config->get(Config::TYPES)) . PHP_EOL);

        return $result;
    }
}
