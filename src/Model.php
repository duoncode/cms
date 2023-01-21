<?php

declare(strict_types=1);

namespace Conia;

use Conia\Puma\Database;

abstract class Model
{
    private static Request $request;
    private static Config $config;

    public static function initialize(Request $request): void
    {
        self::$request = $request;
        self::$config = $request->config();
    }

    public static function db(string $conn = Config::DEFAULT): Database
    {
        static $db = [];

        if (!isset($db[$conn])) {
            $db[$conn] = new Database(self::$config->connection($conn));
        }

        return $db[$conn];
    }
}
