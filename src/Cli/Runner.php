<?php

declare(strict_types=1);

namespace Conia\Cli;

use Chuck\Cli\Runner as BaseRunner;
use Conia\App;


class Runner
{
    public static function run(App $app): string|int
    {
        $result = BaseRunner::run($app->baseApp());

        return $result;
    }
}
