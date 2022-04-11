<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Request as BaseRequest;
use Chuck\Database\{Database, DatabaseInterface};


/**
 * @method session
 */
class Request extends BaseRequest
{
    protected array $dbs = [];

    public function isXHR(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    public function db(
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
