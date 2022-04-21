<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Routing\{Group, Route};
use Chuck\ConfigInterface;
use Conia\App;
use Conia\Middleware\Permission;
use Conia\Middleware\Session;
use Conia\View\{Auth, System, Page};


class Routes
{
    protected string $panelUrl;
    protected string $apiUrl;

    public function __construct(protected ConfigInterface $config)
    {
        $this->panelUrl = $config->debug ? '/panel' : '/' . trim($config->get('panel.path'), '/');
        $this->panelApi = $this->panelUrl . '/api';
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
            '/me',
            [Auth::class, 'me']
        )->middleware(new Permission('authenticated')));
        $api->add(Route::post('auth.login', '/login', [Auth::class, 'login']));
        $api->add(Route::post('auth.logout', '/logout', [Auth::class, 'logout'])->render('json'));
    }

    protected function addUser(Group $api): void
    {
        $permission = new Permission('edit-users');

        $api->add(Route::get('users', 'users', [User::class, 'list'])->middleware($permission));
        $api->add(Route::get('user.get', 'user/{uid}', [User::class, 'get'])->middleware($permission));
        $api->add(Route::post('user.create', 'user', [User::class, 'create'])->middleware($permission));
        $api->add(Route::put('user.save', 'user/{uid}', [User::class, 'save'])->middleware($permission));
    }

    protected function addSettings(Group $api): void
    {
        $api->add(Route::get(
            'conia.settings',
            '/settings',
            [System::class, 'settings'],
        )->render('json'));
    }

    protected function addPanelApi(Group $api): void
    {
        $this->addSettings($api);
        $this->addAuth($api);
        $this->addUser($api);
    }

    public function add(App $app): void
    {
        $session = new Session();

        $this->addIndex($app, $session);

        // All API routes
        $app->group((new Group(
            'conia.panel.',
            $this->panelApi,
            $this->addPanelApi(...)
        ))->middleware($session)->render('json'));

        // Add catchall for page urls. Must be the last one
        $app->add(Route::get(
            'conia:catchall',
            '/...slug',
            [Page::class, 'catchall']
        )->middleware($session));
    }
}
