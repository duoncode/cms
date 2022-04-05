<?php

declare(strict_types=1);

namespace Conia;

use Chuck\{Config, ConfigInterface};
use Chuck\RequestInterface;
use Chuck\Database\{Database, DatabaseInterface};


class Controller
{
    protected ConfigInterface $config;
    protected array $dbs = [];

    public function __construct(protected RequestInterface $request)
    {
        $this->config = $request->getConfig();
    }

    protected function db(
        string $connection = Config::DEFAULT,
        string $sql = 'conia'
    ): DatabaseInterface {
        if ($this->dbs[$connection] ?? null) {
            return $this->dbs[$connection];
        }

        $db = $this->dbs[$connection] = new Database($this->config->db($connection, $sql));

        return $db;
    }
}
