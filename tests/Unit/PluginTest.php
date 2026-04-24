<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Boiler\Renderer as BoilerRenderer;
use Duon\Cms\Plugin;
use Duon\Cms\Renderer;
use Duon\Cms\Tests\Fixtures\StaticRenderer;
use Duon\Cms\Tests\TestCase;
use Duon\Core\App;

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
		$app = $this->loadPlugin(new Plugin());
		$renderer = $app->container()->tag(Renderer::class)->get('view');

		$this->assertInstanceOf(BoilerRenderer::class, $renderer);
		$this->assertSame('<p>plain</p>', trim($renderer->render('plain', [])));
	}

	public function testExplicitViewRendererOverridesDefaultViewRenderer(): void
	{
		$plugin = new Plugin();
		$plugin->renderer('view', StaticRenderer::class);

		$app = $this->loadPlugin($plugin);
		$renderer = $app->container()->tag(Renderer::class)->get('view');

		$this->assertInstanceOf(StaticRenderer::class, $renderer);
		$this->assertSame('custom:plain', $renderer->render('plain', []));
	}

	private function loadPlugin(Plugin $plugin, array $settings = []): App
	{
		$config = $this->config(array_merge([
			'db.dsn' => 'sqlite::memory:',
			'path.root' => self::root() . '/tests/Fixtures/Boiler',
			'path.views' => '/templates',
		], $settings));
		$app = App::create($this->factory(), $config);
		$app->load($plugin);

		return $app;
	}
}
