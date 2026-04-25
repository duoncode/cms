<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Boiler\Renderer as BoilerRenderer;
use Duon\Cms\Config;
use Duon\Cms\Plugin;
use Duon\Cms\Renderer;
use Duon\Cms\Tests\Fixtures\StaticRenderer;
use Duon\Cms\Tests\TestCase;
use Duon\Core\App;
use Duon\Router\Router;

/**
 * @internal
 *
 * @coversNothing
 */
final class PluginTest extends TestCase
{
	public function testConfigProvidesDefaultViewsPath(): void
	{
		$this->assertSame('/views', $this->config()->get('path.views'));
	}

	public function testLoadRegistersDefaultViewRenderer(): void
	{
		$app = $this->loadPlugin();
		$renderer = $app->container()->tag(Renderer::class)->get('view');

		$this->assertInstanceOf(BoilerRenderer::class, $renderer);
		$this->assertSame('<p>plain</p>', trim($renderer->render('plain', [])));
	}

	public function testLoadRegistersConfig(): void
	{
		$app = $this->loadPlugin();

		$this->assertInstanceOf(Config::class, $app->container()->get(Config::class));
	}

	public function testExplicitViewRendererOverridesDefaultViewRenderer(): void
	{
		$app = $this->loadPlugin(static function (Plugin $plugin): void {
			$plugin->renderer('view', StaticRenderer::class);
		});
		$renderer = $app->container()->tag(Renderer::class)->get('view');

		$this->assertInstanceOf(StaticRenderer::class, $renderer);
		$this->assertSame('custom:plain', $renderer->render('plain', []));
	}

	private function loadPlugin(?callable $configure = null, array $settings = []): App
	{
		$config = $this->config(array_merge([
			'db.dsn' => 'sqlite::memory:',
			'path.root' => self::root() . '/tests/Fixtures/Boiler',
			'path.views' => '/templates',
		], $settings));
		$plugin = new Plugin($config);

		if ($configure) {
			$configure($plugin);
		}

		$app = new App($this->factory(), new Router(), $this->container());
		$app->load($plugin);

		return $app;
	}
}
