<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Contract\Icons as IconsContract;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Icons\Iconify;
use Duon\Cms\Icons\Local;
use Duon\Cms\Plugin;
use Duon\Cms\Tests\TestCase;
use ReflectionProperty;

final class PluginIconsTest extends TestCase
{
	public function testIconsPrependsProvider(): void
	{
		$plugin = new Plugin();
		$provider = $this->provider();
		$plugin->icons($provider);
		$providers = $this->providers($plugin);

		$this->assertSame($provider, $providers[0]);
		$this->assertSame(Local::class, $providers[1]);
		$this->assertSame(Iconify::class, $providers[2]);
	}

	public function testIconsReplaceResetsRegistry(): void
	{
		$plugin = new Plugin();
		$provider = $this->provider();
		$plugin->icons($provider, replace: true);
		$providers = $this->providers($plugin);

		$this->assertCount(1, $providers);
		$this->assertSame($provider, $providers[0]);
	}

	public function testIconsRejectsInvalidClassString(): void
	{
		$plugin = new Plugin();
		$this->throws(RuntimeException::class, 'Icons providers must implement ' . IconsContract::class);
		$plugin->icons(self::class);
	}

	private function provider(): IconsContract
	{
		return new class implements IconsContract {
			public function icon(
				string $id,
				?string $color = null,
				?string $class = null,
				?string $style = null,
			): string {
				return '';
			}
		};
	}

	private function providers(Plugin $plugin): array
	{
		$property = new ReflectionProperty($plugin, 'iconProviders');

		return $property->getValue($plugin);
	}
}
