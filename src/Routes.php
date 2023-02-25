<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Group;
use Conia\Core\App;
use Conia\Core\Middleware\InitRequest;
use Conia\Core\Middleware\Permission;
use Conia\Core\Middleware\Session;
use Conia\Core\View\Auth;
use Conia\Core\View\Page;
use Conia\Core\View\Panel;

class Routes
{
    protected string $panelPath;
    protected string $apiPath;

    public function __construct(protected Config $config)
    {
        $this->panelPath = $config->getPanelPath();
        $this->apiPath = $this->panelPath . '/api';
    }

    public function add(App $app): void
    {
        $this->addIndex($app);

        // All API routes
        $app->group(
            $this->apiPath,
            $this->addPanelApi(...),
            'conia.panel.',
        )->render('json');

        // Add catchall for page url paths. Must be the last one
        $app->get(
            '/...slug',
            [Page::class, 'catchall'],
            'conia:catchall',
        )->middleware(Session::class, InitRequest::class);
    }

    protected function addIndex(App $app): void
    {
        $app->get($this->panelPath, fn () => '<h1>Panel not found in public directory</h1>')
            ->render('text', contentType: 'text/html');
    }

    protected function addAuth(Group $api): void
    {
        $api->get('/me', [Auth::class, 'me'], 'auth.user')
            ->middleware(new Permission('authenticated'));
        $api->post('/login', [Auth::class, 'login'], 'auth.login');
        $api->post('/logout', [Auth::class, 'logout'], 'auth.logout')->render('json');
    }

    protected function addUser(Group $api): void
    {
        $editUsers = new Permission('edit-users');

        $api->get('users', [User::class, 'list'], 'users')->middleware($editUsers);
        $api->get('user/{uid}', [User::class, 'get'], 'user.get')->middleware($editUsers);
        $api->post('user', [User::class, 'create'], 'user.create')->middleware($editUsers);
        $api->put('user/{uid}', [User::class, 'save'], 'user.save')->middleware($editUsers);
    }

    protected function addSettings(Group $api): void
    {
        $api->get('/settings', [Panel::class, 'settings'], 'conia.settings');
    }

    protected function addSystem(Group $api): void
    {
        $panel = new Permission('panel');

        $api->get('/boot', [Panel::class, 'boot'], 'conia.boot')->middleware($panel);
        $api->get('/type/{name}', [Panel::class, 'type'], 'conia.type')->middleware($panel);
    }

    protected function addPanelApi(Group $api): void
    {
        $this->addSettings($api);
        $this->addAuth($api);
        $this->addUser($api);
        $this->addSystem($api);
    }
}
