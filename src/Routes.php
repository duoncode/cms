<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Routing\{Group, Route};
use Chuck\Middleware\Session;
use Conia\App;
use Conia\Config;
use Conia\Viev\{Index, System};


class Routes
{
    protected string $panelUrl;
    protected string $apiUrl;

    public function __construct(protected App $app, Config $config)
    {
        $this->panelUrl = rtrim($config->get('conia.panelurl'), '/');
        $this->apiUrl = $this->panelUrl . '/api';
        $this->addIndex();
        $this->addApi();
    }

    protected function addIndex(): void
    {
        $this->app->add(Route::get(
            'conia.index',
            $this->panelUrl,
            '\Conia\View\Index',
        ));
    }

    protected function addApi(): void
    {
        $this->app->group((new Group(
            'conia.system.',
            $this->apiUrl,
            $this->systemRoutes(...)
        ))->middleware(new Session()));
    }

    protected function systemRoutes(Group $group): void
    {
        $group->add(Route::get(
            'settings',
            '/settings',
            [System::class, 'settings'],
        )->render('json'));
    }

    public static function addCatchall(App $app): void
    {
        $app->add(new Route('conia:catchall', '/...slug', 'Page::catchall'));
    }
}
