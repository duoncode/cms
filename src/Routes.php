<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Routing\{Group, Route};
use Chuck\Middleware\Session;
use Chuck\ConfigInterface;
use Conia\App;
use Conia\Middleware\Permission;
use Conia\View\{System, Page};


class Routes
{
    protected string $panelUrl;
    protected string $apiUrl;

    public function __construct(protected ConfigInterface $config)
    {
        $this->panelUrl = rtrim($config->get('panel.path'), '/');
        $this->apiUrl = $this->panelUrl . '/api';
    }

    protected function addIndex(App $app, Session $session): void
    {
        $app->add(Route::get(
            'conia.index',
            $this->panelUrl,
            fn () => '<h1>Panel not found in public directory</h1>',
        )->render('text', contentType: 'text/html')->middleware($session));
    }

    protected function addAuth(Group $api): void
    {
        $api->add(Route::get(
            'auth.user',
            '/currentuser',
            [Auth::class, 'user']
        )->middleware(new Permission('authenticated')));
        $api->add(Route::post('auth.login', '/login', [Auth::class, 'login'],));
        $api->add(Route::get('auth.logout', '/logout', [Auth::class, 'logout'],));
    }

    protected function addUser(Group $api): void
    {
        $permission = new Permission('edit-users');

        $api->add(Route::get('users', 'users', [User::class, 'list'])->middleware($permission));
        $api->add(Route::get('user.get', 'user/{uid}', [User::class, 'get'])->middleware($permission));
        $api->add(Route::post('user.create', 'user', [User::class, 'create'])->middleware($permission));
        $api->add(Route::put('user.save', 'user/{uid}', [User::class, 'save'])->middleware($permission));
    }

    protected function addApi(Group $api): void
    {
        $this->addAuth($api);
        $this->addUser($api);
    }

    protected function addSettings(App $app): void
    {
        $app->add(Route::get(
            'conia.settings',
            $this->panelUrl . '/settings',
            [System::class, 'settings'],
        )->render('json'));
    }

    public function add(App $app): void
    {
        $session = new Session();

        $this->addIndex($app, $session);
        $this->addSettings($app);

        // All API routes
        $app->group((new Group(
            'conia.api.',
            $this->apiUrl,
            $this->addApi(...)
        ))->middleware($session)->render('json'));

        // Add catchall for page urls. Must be the last one
        $app->add(Route::get(
            'conia:catchall',
            '/...slug',
            [Page::class, 'catchall']
        )->middleware($session));
    }
}
