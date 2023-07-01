<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Group;
use Conia\Core\App;
use Conia\Core\Middleware;
use Conia\Core\View\Auth;
use Conia\Core\View\Media;
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
        );

        $app->route($this->panelPath . '/...slug', [Panel::class, 'catchall'], 'conia.panel.catchall')
            ->middleware(Middleware\InitRequest::class);
        $app->route($this->panelPath, [Panel::class, 'index'], 'conia.panel')
            ->middleware(Middleware\InitRequest::class);

        $app->post('/media/{type:(image|file)}/{doctype:(node|menu)}/{uid:[a-z0-9-]{1,64}}', [Media::class, 'upload'], 'conia.media.upload')
            ->middleware(Middleware\InitRequest::class, Middleware\Session::class);
        $app->get('/media/image/...slug', [Media::class, 'image'], 'conia.media.image')
            ->middleware(Middleware\InitRequest::class);
        $app->get('/media/file/...slug', [Media::class, 'file'], 'conia.media.file')
            ->middleware(Middleware\InitRequest::class);

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
        $api->get('/users', [User::class, 'list'], 'users');
        $api->get('/user/{uid:[a-z0-9-]{1,64}}', [User::class, 'get'], 'user.get');
        $api->post('/user', [User::class, 'create'], 'user.create');
        $api->put('/user/{uid:[a-z0-9-]{1,64}}', [User::class, 'save'], 'user.save');
    }

    protected function addSystem(Group $api): void
    {
        $api->get('/boot', [Panel::class, 'boot'], 'conia.boot');
        $api->get('/collections', [Panel::class, 'collections'], 'conia.collections');
        $api->get('/collection/{collection}', [Panel::class, 'collection'], 'conia.collection');
        $api->route('/node/{uid:[a-z0-9-]{1,64}}', [Panel::class, 'node'], 'conia.node')->method('GET', 'PUT', 'DELETE');
        $api->post('/node/{type}', [Panel::class, 'createNode'], 'conia.node.create');
        $api->get('/blueprint/{type}', [Panel::class, 'blueprint'], 'conia.blueprint');
    }

    protected function addPanelApi(Group $api): void
    {
        $api->middleware(Middleware\InitRequest::class, Middleware\Session::class)->render('json');

        $this->addAuth($api);
        $this->addUser($api);
        $this->addSystem($api);
    }
}
