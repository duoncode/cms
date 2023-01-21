<?php

declare(strict_types=1);

namespace Conia\Core\Tests\Setup;

use Conia\Core\Config;
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

    public function config(bool $debug = false): Config
    {
        return new Config('conia', debug: $debug);
    }
}
