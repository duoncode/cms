<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

use FiveOrbs\Cms\Config;
use FiveOrbs\Cms\Middleware\InitRequest;
use FiveOrbs\Cms\Middleware\Session;
use FiveOrbs\Cms\View\Auth;
use FiveOrbs\Cms\View\Media;
use FiveOrbs\Cms\View\Page;
use FiveOrbs\Cms\View\Panel;
use FiveOrbs\Cms\View\User;
use FiveOrbs\Core\App;
use FiveOrbs\Core\Factory;
use FiveOrbs\Quma\Database;
use FiveOrbs\Router\Group;
use FiveOrbs\Router\Route;

class Routes
{
	protected string $panelPath;
	protected string $apiPath;
	protected string $panelApiPath;
	protected InitRequest $initRequestMiddlware;

	public function __construct(
		protected Config $config,
		protected Database $db,
		protected Factory $factory,
		protected bool $sessionEnabled,
	) {
		$this->panelPath = $config->get('panel.prefix');
		$this->apiPath = '/api';
		$this->panelApiPath = $this->panelPath . $this->apiPath;
		$this->initRequestMiddlware = new InitRequest($config);
	}

	public function add(App $app): void
	{
		$session = new Session($this->config, $this->db);

		$indexRoute = $app->get('/', [Page::class, 'catchall'], 'cms.index.get');
		$indexRoute = $app->post('/', [Page::class, 'catchall'], 'cms.index.post');

		// All API routes
		$this->addApi($app);
		$this->addPanelApi($app, $session);

		$app->get($this->panelPath . '/...slug', [Panel::class, 'catchall'], 'cms.panel.catchall');
		$app->get($this->panelPath, [Panel::class, 'index'], 'cms.panel');
		$app->get($this->panelPath . '/', [Panel::class, 'index'], 'cms.panel.slash');

		$postMediaRoute = $app->post(
			'/media/{mediatype:(image|file|video)}/{doctype:(node|menu)}/{uid:[A-Za-z0-9-]{1,64}}',
			[Media::class, 'upload'],
			'cms.media.upload',
		);

		$app->get('/media/image/...slug', [Media::class, 'image'], 'cms.media.image');
		$app->get('/media/file/...slug', [Media::class, 'file'], 'cms.media.file');
		$app->get('/media/video/...slug', [Media::class, 'file'], 'cms.media.video');

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
		$api->get('/boot', [Panel::class, 'boot'], 'boot');
		$api->get('/collections', [Panel::class, 'collections'], 'collections');
		$api->get('/collection/{collection}', [Panel::class, 'collection'], 'collection');
		$api->get('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.get');
		$api->put('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.put');
		$api->delete('/node/{uid:[A-Za-z0-9-]{1,64}}', [Panel::class, 'node'], 'node.delet');
		$api->post('/node/{type}', [Panel::class, 'createNode'], 'node.create');
		$api->get('/blueprint/{type}', [Panel::class, 'blueprint'], 'blueprint');
	}

	protected function addApi(App $app): void
	{
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
}
