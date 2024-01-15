<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\App;
use Conia\Core\Middleware\Session;
use Conia\Core\View\Auth;
use Conia\Core\View\Media;
use Conia\Core\View\Page;
use Conia\Core\View\Panel;
use Conia\Core\View\User;
use Conia\Quma\Database;
use Conia\Route\Group;

class Routes
{
    protected string $panelPath;
    protected string $apiPath;

    public function __construct(
        protected Config $config,
        protected Database $db,
        protected Factory $factory,
        protected bool $sessionEnabled
    ) {
        $this->panelPath = $config->getPanelPath();
        $this->apiPath = $this->panelPath . '/api';
    }

    public function add(App $app): void
    {
        $session = new Session($this->config, $this->db);

        // All API routes
        $this->addPanelApi($app, $session);

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
            $postMediaRoute->middleware($session);
            $catchallRoute->middleware($session);
        }
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

    protected function addPanelApi(App $app, Session $session): void
    {
        $app->group(
            $this->apiPath,
            function (Group $api) use ($session) {
                $api->after(new JsonRenderer($this->factory));

                if (!$this->sessionEnabled) {
                    $api->middleware($session);
                }

                $this->addAuth($api);
                $this->addUser($api);
                $this->addSystem($api);
            },
            'conia.panel.',
        );
    }
}
