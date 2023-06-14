<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Group;
use Conia\Core\App;
use Conia\Core\Middleware;
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
        // All API routes
        $app->group(
            $this->apiPath,
            $this->addPanelApi(...),
            'conia.panel.',
        )->render('json');

        $app->route('/panel/...slug', [Panel::class, 'catchall'], 'conia.panel.catchall')->middleware(Middleware\InitRequest::class);
        $app->route('/panel', [Panel::class, 'index'], 'conia.panel')->middleware(Middleware\InitRequest::class);

        // Add catchall for page url paths. Must be the last one
        $app->route(
            '/...slug',
            [Page::class, 'catchall'],
            'conia.catchall',
        )->middleware(Middleware\InitRequest::class);
    }

    protected function addAuth(Group $api): void
    {
        $api->get('/me', [Auth::class, 'me'], 'auth.user');
        $api->post('/login', [Auth::class, 'login'], 'auth.login');
        $api->post('/logout', [Auth::class, 'logout'], 'auth.logout');
    }

    protected function addUser(Group $api): void
    {
        $api->get('users', [User::class, 'list'], 'users');
        $api->get('user/{uid}', [User::class, 'get'], 'user.get');
        $api->post('user', [User::class, 'create'], 'user.create');
        $api->put('user/{uid}', [User::class, 'save'], 'user.save');
    }

    protected function addSystem(Group $api): void
    {
        $api->get('/boot', [Panel::class, 'boot'], 'conia.boot');
        $api->get('/collection/{collection}', [Panel::class, 'collection'], 'conia.collection');
    }

    protected function addPanelApi(Group $api): void
    {
        $api->middleware(Middleware\Session::class)->render('json');

        $this->addAuth($api);
        $this->addUser($api);
        $this->addSystem($api);
    }
}
