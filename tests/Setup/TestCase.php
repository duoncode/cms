<?php

declare(strict_types=1);

namespace Conia\Core\Tests\Setup;

use Conia\Core\Config;
use Conia\Quma\Connection;
use Conia\Quma\Database;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TestCase extends BaseTestCase
{
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function config(array $settings = [], bool $debug = false): Config
    {
        return new Config('conia', debug: $debug, settings: $settings);
    }

    public function conn(): Connection
    {
        return new Connection(
            'pgsql:host=localhost;dbname=conia_db;user=conia_user;password=conia_password',
            C::root() . '/db/sql',
            C::root() . '/db/migrations',
            fetchMode: PDO::FETCH_ASSOC,
            print: false,
        );
    }

    public function db(): Database
    {
        return new Database($this->conn());
    }
}
