<?php

declare(strict_types=1);

namespace Conia;

use Conia\App;

class Routes {
    public static function add(App $app): void
    {
        $app->get(
            'conia:index',
            '/system',
            '\Conia\Controller\System::index',
            'conia::index'
        );
    }

    public static function addCatchall(App $app): void
    {
        $app->get('conia:catchall', '/...slug', '\Conia\Controller\Page::catchall');
    }
}
