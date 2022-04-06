<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Routing\{Group, Route};
use Chuck\Middleware\Session;
use Chuck\ConfigInterface;
use Conia\App;
use Conia\View\{System, Page};


class Routes
{
    protected string $panelUrl;
    protected string $apiUrl;

    public function __construct(protected ConfigInterface $config)
    {
        $this->panelUrl = rtrim($config->get('panel.slug'), '/');
        $this->apiUrl = $this->panelUrl . '/api';
    }

    protected function addIndex(App $app): void
    {
        $app->add(Route::get(
            'conia.index',
            $this->panelUrl,
            fn () => '<h1>Panel not found in public directory</h1>',
        )->render('text', contentType: 'text/html'));
    }

    protected function addApi(Group $api): void
    {
        $this->addSettings($api);
    }

    protected function addSettings(Group $api): void
    {
        $api->add(Route::get(
            'settings',
            '/settings',
            [System::class, 'settings'],
        )->render('json'));
    }

    public function add(App $app): void
    {
        $this->addIndex($app);

        $app->group((new Group(
            'conia.api.',
            $this->apiUrl,
            $this->addApi(...)
        ))->middleware(new Session()));

        $app->add(new Route('conia:catchall', '/...slug', [Page::class, 'catchall']));
    }
}
