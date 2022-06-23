<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Database\Database;


abstract class Model
{
    protected static Request $request;
    protected static Config $config;


    public static function init(Request $request): void
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
