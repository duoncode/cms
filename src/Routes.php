<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Cms\Config;
use Conia\Cms\Middleware\InitRequest;
use Conia\Cms\Middleware\Session;
use Conia\Cms\View\Auth;
use Conia\Cms\View\Media;
use Conia\Cms\View\Page;
use Conia\Cms\View\Panel;
use Conia\Cms\View\User;
use Conia\Core\App;
use Conia\Core\Factory;
use Conia\Quma\Database;
use Conia\Route\Group;
use Conia\Route\Route;

class Routes
{
    protected string $panelPath;
    protected string $apiPath;
    protected InitRequest $initRequestMiddlware;

    public function __construct(
        protected Config $config,
        protected Database $db,
        protected Factory $factory,
        protected bool $sessionEnabled
    ) {
        $this->panelPath = $config->get('panel.prefix');
        $this->apiPath = $this->panelPath . '/api';
        $this->initRequestMiddlware = new InitRequest($config);
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

    public function catchallRoute(): Route
    {
        return Route::any(
            '/...slug',
            [Page::class, 'catchall'],
            'conia.catchall',
        )->middleware($this->initRequestMiddlware);
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
