<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Icons;
use Duon\Cms\Tests\TestCase;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class IconsTest extends TestCase
{
	public function testCacheMissFetchesAndStoresSvg(): void
	{
		$publicDir = $this->publicDir();
		$calls = 0;

		try {
			$icons = $this->icons(
				$publicDir,
				function (string $url, int $timeout, string $userAgent) use (&$calls): string {
					$calls++;
					$this->assertSame('https://api.iconify.design/bi/check.svg', $url);
					$this->assertSame(5, $timeout);
					$this->assertSame('duon/cms', $userAgent);

					return '<svg viewBox="0 0 16 16"></svg>';
				},
			);
			$svg = $icons->icon('bi:check');
			$cacheFile = $publicDir . '/cache/icons/' . hash('xxh3', 'bi:check') . '.svg';

			$this->assertSame(1, $calls);
			$this->assertSame('<svg viewBox="0 0 16 16"></svg>', $svg);
			$this->assertFileExists($cacheFile);
			$this->assertSame('<svg viewBox="0 0 16 16"></svg>', file_get_contents($cacheFile));
		} finally {
			$this->removeDir($publicDir);
		}
	}

	public function testCacheHitSkipsFetch(): void
	{
		$publicDir = $this->publicDir();
		$calls = 0;

		try {
			$cacheDir = $publicDir . '/cache/icons';
			mkdir($cacheDir, 0o755, true);
			$cacheFile = $cacheDir . '/' . hash('xxh3', 'bi:check') . '.svg';
			file_put_contents($cacheFile, '<svg data-cache="1"></svg>');

			$icons = $this->icons(
				$publicDir,
				function () use (&$calls): ?string {
					$calls++;

					return null;
				},
			);
			$svg = $icons->icon('bi:check');

			$this->assertSame(0, $calls);
			$this->assertSame('<svg data-cache="1"></svg>', $svg);
		} finally {
			$this->removeDir($publicDir);
		}
	}

	public function testInvalidIconIdReturnsEmptyStringWithoutFetch(): void
	{
		$publicDir = $this->publicDir();
		$calls = 0;

		try {
			$icons = $this->icons(
				$publicDir,
				function () use (&$calls): string {
					$calls++;

					return '<svg></svg>';
				},
			);

			$this->assertSame('', $icons->icon('invalid'));
			$this->assertSame(0, $calls);
		} finally {
			$this->removeDir($publicDir);
		}
	}

	public function testInvalidResponseReturnsEmptyString(): void
	{
		$publicDir = $this->publicDir();

		try {
			$icons = $this->icons(
				$publicDir,
				static fn(): string => 'not-svg',
			);
			$cacheFile = $publicDir . '/cache/icons/' . hash('xxh3', 'bi:check') . '.svg';

			$this->assertSame('', $icons->icon('bi:check'));
			$this->assertFileDoesNotExist($cacheFile);
		} finally {
			$this->removeDir($publicDir);
		}
	}

	public function testIconAddsClassStyleAndColorToSvgTag(): void
	{
		$publicDir = $this->publicDir();

		try {
			$icons = $this->icons(
				$publicDir,
				static fn(): string => '<svg class="base" style="display: block"></svg>',
			);
			$svg = $icons->icon(
				'bi:check',
				color: '#ff0000',
				class: 'extra',
				style: 'height: 2rem',
			);

			$this->assertStringContainsString('class="base extra"', $svg);
			$this->assertStringContainsString('display: block', $svg);
			$this->assertStringContainsString('height: 2rem', $svg);
			$this->assertStringContainsString('color: #ff0000', $svg);
		} finally {
			$this->removeDir($publicDir);
		}
	}

	public function testCacheDirectoryIsCreatedAutomatically(): void
	{
		$publicDir = $this->publicDir();

		try {
			$icons = $this->icons(
				$publicDir,
				static fn(): string => '<svg></svg>',
			);
			$icons->icon('bi:check');

			$this->assertDirectoryExists($publicDir . '/cache/icons');
		} finally {
			$this->removeDir($publicDir);
		}
	}

	private function icons(string $publicDir, callable $fetch): Icons
	{
		$config = $this->config([
			'path.public' => $publicDir,
			'path.cache' => '/cache',
		]);

		return new Icons($config, $fetch);
	}

	private function publicDir(): string
	{
		$dir = sys_get_temp_dir() . '/duon-cms-icons-' . bin2hex(random_bytes(8));
		mkdir($dir, 0o755, true);

		return $dir;
	}

	private function removeDir(string $path): void
	{
		if (!is_dir($path)) {
			return;
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST,
		);

		foreach ($iterator as $item) {
			if ($item->isDir()) {
				rmdir($item->getPathname());
				continue;
			}

			unlink($item->getPathname());
		}

		rmdir($path);
	}
}
