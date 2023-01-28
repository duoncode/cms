<?php

declare(strict_types=1);

use Conia\Core\Tests\Setup\TestCase;

uses(TestCase::class);

test('Compile query', function () {
    expect('conia')->toBe('conia');
});
