<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Config;
use Duon\Cms\Middleware\InitRequest;
use Duon\Cms\Middleware\Session;
use Duon\Cms\View\Auth;
use Duon\Cms\View\Media;
use Duon\Cms\View\Nodes;
use Duon\Cms\View\Page;
use Duon\Cms\View\Panel;
use Duon\Cms\View\User;
use Duon\Core\App;
use Duon\Core\Factory;
use Duon\Quma\Database;
use Duon\Router\Group;
use Duon\Router\Route;

class Routes
{
	protected string $panelPath;
	protected string $panelApiPath;
	protected string|null $apiPath;
	protected InitRequest $initRequestMiddlware;

	public function __construct(
		protected Config $config,
		protected Database $db,
		protected Factory $factory,
		protected bool $sessionEnabled,
	) {
		$this->panelPath = $config->panelPath();
		$this->panelApiPath = $this->panelPath . '/api';
		$this->apiPath = $config->apiPath();
		$this->initRequestMiddlware = new InitRequest($config);
	}

	public function add(App $app): void
	{
		$session = new Session($this->config, $this->db);

		$indexRoute = $app->get('/', [Page::class, 'catchall'], 'cms.index.get');
		$indexRoute = $app->post('/', [Page::class, 'catchall'], 'cms.index.post');

		$this->addPanelApi($app, $session);
		$this->addApi($app, $session);

		$postMediaRoute = $app->post(
			'/media/{mediatype:(image|file|video)}/{doctype:(node|menu)}/{uid:[A-Za-z0-9-]{1,64}}',
			[Media::class, 'upload'],
			'cms.media.upload',
		);

		$app->get('/media/image/...slug', [Media::class, 'image'], 'cms.media.image');
		$app->get('/media/file/...slug', [Media::class, 'file'], 'cms.media.file');
		$app->get('/media/video/...slug', [Media::class, 'file'], 'cms.media.video');

		$app->get(
			$this->panelPath . '/boot',
			[Panel::class, 'boot'],
			'cms.panel.boot',
		)->after(new JsonRenderer($this->factory));
		$app->get($this->panelPath . '/...slug', [Panel::class, 'catchall'], 'cms.panel.catchall');
		$app->get($this->panelPath, [Panel::class, 'index'], 'cms.panel');
		$app->get($this->panelPath . '/', [Panel::class, 'index'], 'cms.panel.slash');

		$catchallRoute = $app->get('/preview/...slug', [Page::class, 'preview'], 'cms.preview.catchall');

		if (!$this->sessionEnabled) {
			$indexRoute->middleware($session);
			$postMediaRoute->middleware($session);
			$catchallRoute->middleware($session);
		}
	}

	public function catchallRoute(): Route
	{
		return Route::any(
			'/...slug',
			[Page::class, 'catchall'],
			'cms.catchall',
		)->method('GET', 'POST')->middleware($this->initRequestMiddlware);
	}

	protected function addAuth(Group $api): void
	{
		$api->get('/me', [Auth::class, 'me'], 'auth.user');
		$api->post('/login', [Auth::class, 'login'], 'auth.login');
		$api->post('/token-login', [Auth::class, 'tokenLogin'], 'auth.login.token');
		$api->post('/invalidate-token', [Auth::class, 'invalidateToken'], 'auth.token.invalidate');
		$api->get('/login/token', [Auth::class, 'token'], 'auth.token');
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
		$api->get('/collections', [Panel::class, 'collections'], 'collections');
		$api->get('/collection/{collection}', [Panel::class, 'collection'], 'collection');
		$api->get('/nodes', [Nodes::class, 'get'], 'nodes.get');
		$api->get('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.get');
		$api->put('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.update');
		$api->delete('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.delete');
		$api->post('/node/{type}', [Panel::class, 'createNode'], 'node.create');
		$api->get('/blueprint/{type}', [Panel::class, 'blueprint'], 'node.blueprint');
	}

	protected function addPanelApi(App $app, Session $session): void
	{
		$app->group(
			$this->panelApiPath,
			function (Group $api) use ($session) {
				$api->after(new JsonRenderer($this->factory));

				if (!$this->sessionEnabled) {
					$api->middleware($session);
				}

				$this->addAuth($api);
				$this->addUser($api);
				$this->addSystem($api);
			},
			'cms.panel.api.',
		);
	}

	protected function addApi(App $app): void
	{
		if ($this->apiPath !== null) {
			$app->group(
				$this->apiPath,
				function (Group $api) {
					$api->after(new JsonRenderer($this->factory));

					$this->addAuth($api);
					$this->addUser($api);
					$this->addSystem($api);
				},
				'cms.api.',
			);
		}
	}
}
