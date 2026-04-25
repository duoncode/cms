<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\App;
use Duon\Cms\Config;
use Duon\Cms\Plugin;
use Duon\Cms\Tests\Fixtures\Collection\TestArticlesCollection;
use Duon\Cms\Tests\TestCase;
use Duon\Core\App as CoreApp;
use Duon\Core\Plugin as CorePlugin;
use Duon\Core\Response as CoreResponse;
use Duon\Router\Router;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class AppTest extends TestCase
{
	public function testCreateHelperBuildsCoreAppAndPlugin(): void
	{
		$config = $this->appConfig();
		$app = App::create($config);

		$this->assertSame($config, $app->config());
		$this->assertInstanceOf(CoreApp::class, $app->core());
		$this->assertInstanceOf(Plugin::class, $app->plugin());
	}

	public function testCmsMethodsConfigureInternalPlugin(): void
	{
		$app = $this->app();

		$app->section('Content')->collection(TestArticlesCollection::class);
		$app->node(\Duon\Cms\Tests\Fixtures\Node\TestArticle::class);

		$this->assertArrayHasKey('test-articles', $app->navigation()->refs());
		$this->assertSame(
			'test-article',
			$app->meta()->get(\Duon\Cms\Tests\Fixtures\Node\TestArticle::class, 'handle'),
		);
	}

	public function testBootLoadsCmsPluginAndCatchallRoute(): void
	{
		$app = $this->app();

		$this->assertFalse($app->container()->has(Config::class));

		$app->boot()->boot();
		$route = $app->router()->match(
			$app->factory()->serverRequestFactory()->createServerRequest('GET', '/missing'),
		);

		$this->assertSame($app->config(), $app->container()->get(Config::class));
		$this->assertSame('cms.catchall', $route->name());
	}

	public function testCoreMethodsDelegateToInternalCoreApp(): void
	{
		$app = $this->app(['path.prefix' => '/site']);
		$app->get(
			'/ok',
			static fn(): CoreResponse => CoreResponse::create($app->factory())->body('ok'),
		);

		$request = $app->factory()->serverRequestFactory()->createServerRequest('GET', '/site/ok');
		ob_start();

		try {
			$response = $app->run($request);
			$output = ob_get_contents();
		} finally {
			ob_end_clean();
		}

		$this->assertInstanceOf(ResponseInterface::class, $response);
		$this->assertSame('ok', $output);
		$this->assertSame($app->config(), $app->container()->get(Config::class));
	}

	public function testLoadDelegatesToCoreApp(): void
	{
		$plugin = new class implements CorePlugin {
			public function load(CoreApp $app): void
			{
				$app->register('custom.service', self::class)->value();
			}
		};
		$app = $this->app();

		$app->load($plugin);

		$this->assertSame($plugin::class, $app->container()->get('custom.service'));
	}

	private function app(array $settings = []): App
	{
		$config = $this->appConfig($settings);

		return new App(
			$config,
			$this->factory(),
			new Router((string) $config->get('path.prefix')),
			$this->container(),
		);
	}

	private function appConfig(array $settings = []): Config
	{
		return $this->config(array_merge([
			'db.dsn' => 'sqlite::memory:',
			'path.root' => self::root() . '/tests/Fixtures/Boiler',
			'path.views' => '/templates',
		], $settings));
	}
}
