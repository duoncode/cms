<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Group;
use Conia\Core\App;
use Conia\Core\Session;
use Conia\Core\View\Auth;
use Conia\Core\View\Media;
use Conia\Core\View\Page;
use Conia\Core\View\Panel;
use Conia\Core\View\User;

class Routes
{
    protected string $panelPath;
    protected string $apiPath;

    public function __construct(protected Config $config, protected bool $sessionEnabled)
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

        $app->route($this->panelPath . '/...slug', [Panel::class, 'catchall'], 'conia.panel.catchall');
        $app->route($this->panelPath . '/', [Panel::class, 'index'], 'conia.panel.slash');
        $app->route($this->panelPath, [Panel::class, 'index'], 'conia.panel');

        $postMediaRoute = $app->post('/media/{mediatype:(image|file)}/{doctype:(node|menu)}/{uid:[A-Za-z0-9-]{1,64}}', [Media::class, 'upload'], 'conia.media.upload');

        $app->get('/media/image/...slug', [Media::class, 'image'], 'conia.media.image');
        $app->get('/media/file/...slug', [Media::class, 'file'], 'conia.media.file');

        $catchallRoute = $app->route(
            '/preview/...slug',
            [Page::class, 'preview'],
            'conia.preview.catchall',
        );

        if (!$this->sessionEnabled) {
            $postMediaRoute->middleware(Session::class);
            $catchallRoute->middleware(Session::class);
        }

        // Add catchall for page url paths. Must be the last one
        $app->route(
            '/...slug',
            [Page::class, 'catchall'],
            'conia.catchall',
        );
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
        $api->get('/user/{uid:[A-Za-z0-9-]{1,64}}', [User::class, 'get'], 'user.get');
        $api->post('/user', [User::class, 'create'], 'user.create');
        $api->put('/user/{uid:[A-Za-z0-9-]{1,64}}', [User::class, 'save'], 'user.save');
        $api->get('/profile', [User::class, 'profile'], 'profile.get');
        $api->put('/profile', [User::class, 'saveProfile'], 'profile.save');
    }

    protected function addSystem(Group $api): void
    {
        $api->get('/boot', [Panel::class, 'boot'], 'conia.boot');
        $api->get('/collections', [Panel::class, 'collections'], 'conia.collections');
        $api->get('/collection/{collection}', [Panel::class, 'collection'], 'conia.collection');
        $api->route('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'conia.node')->method('GET', 'PUT', 'DELETE');
        $api->post('/node/{type}', [Panel::class, 'createNode'], 'conia.node.create');
        $api->get('/blueprint/{type}', [Panel::class, 'blueprint'], 'conia.blueprint');
    }

    protected function addPanelApi(Group $api): void
    {
        $api->render('json');

        if (!$this->sessionEnabled) {
            $api->middleware(Session::class);
        }

        $this->addAuth($api);
        $this->addUser($api);
        $this->addSystem($api);
    }
}
