<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Tests\TestCase;
use Duon\Cms\View\Panel\Assets;
use Duon\Core\Exception\HttpNotFound;
use Duon\Core\Request;

/**
 * @internal
 *
 * @coversNothing
 */
final class PanelAssetTest extends TestCase
{
	public function testAssetReturnsNotFoundForPathTraversal(): void
	{
		$panel = new Assets($this->config(), $this->container(), $this->request());

		$this->throws(HttpNotFound::class);
		$panel->asset($this->request(), $this->factory(), '../composer.json');
	}

	public function testAssetReturnsNotModifiedWhenEtagMatches(): void
	{
		$panel = new Assets($this->config(), $this->container(), $this->request());
		$file = self::root() . '/panel/styles/app.css';
		$etag = md5_file($file);
		$this->assertNotFalse($etag);
		$request = new Request($this->psrRequest()->withHeader('If-None-Match', '"' . $etag . '"'));

		$response = $panel->asset($request, $this->factory(), 'styles/app.css');

		$this->assertSame(304, $response->getStatusCode());
		$this->assertSame(['private, max-age=3600'], $response->getHeader('Cache-Control'));
		$this->assertSame(['"' . $etag . '"'], $response->getHeader('ETag'));
		$this->assertSame([], $response->getHeader('Content-Type'));
	}

	public function testAssetReturnsCssFileWithCacheHeaders(): void
	{
		$panel = new Assets($this->config(), $this->container(), $this->request());
		$file = self::root() . '/panel/styles/app.css';
		$etag = md5_file($file);
		$this->assertNotFalse($etag);

		$response = $panel->asset($this->request(), $this->factory(), 'styles/app.css');

		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame(['text/css'], $response->getHeader('Content-Type'));
		$this->assertSame(['private, max-age=3600'], $response->getHeader('Cache-Control'));
		$this->assertSame(['"' . $etag . '"'], $response->getHeader('ETag'));
		$this->assertNotSame([], $response->getHeader('Last-Modified'));
		$this->assertSame(file_get_contents($file), (string) $response->getBody());
	}
}
