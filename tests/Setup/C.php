<?php

declare(strict_types=1);

namespace Conia\Cms\Tests\Setup;

class C
{
    public static function root(): string
    {
        return dirname(dirname(__DIR__));
    }
}
