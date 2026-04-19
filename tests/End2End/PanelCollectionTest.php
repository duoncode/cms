<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\End2End;

use Duon\Cms\Plugin;
use Duon\Cms\Tests\End2EndTestCase;
use Duon\Cms\Tests\Fixtures\Collection\TestArticlesCollection;

final class PanelCollectionTest extends End2EndTestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		$this->loadFixtures('basic-types');
		$this->authenticateAs('editor');
	}

	protected function createPlugin(): Plugin
	{
		$plugin = parent::createPlugin();
		$plugin->section('Inhalt')->collection(TestArticlesCollection::class);

		return $plugin;
	}

	public function testPanelCollectionRouteResolvesKnownCollection(): void
	{
		$response = $this->makeRequest('GET', '/panel/collection/test-articles');

		$this->assertResponseOk($response);
		$html = $this->getHtmlResponse($response);
		$this->assertStringContainsString('Collection', $html);
	}

	public function testPanelCollectionRouteReturnsNotFoundForUnknownCollection(): void
	{
		$response = $this->makeRequest('GET', '/panel/collection/does-not-exist');

		$this->assertResponseStatus(404, $response);
	}
}
