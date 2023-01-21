<?php

declare(strict_types=1);

use Conia\Core\Config;
use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

test('Add database connection', function () {
    new Config('chuck');
});
