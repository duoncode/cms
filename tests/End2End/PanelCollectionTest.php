<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\End2End;

use Duon\Cms\Plugin;
use Duon\Cms\Tests\End2EndTestCase;
use Duon\Cms\Tests\Fixtures\Collection\TestArticlesCollection;

final class PanelCollectionTest extends End2EndTestCase
{
	private ?int $articleTypeId = null;

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

	public function testPanelCollectionRouteRendersGridListWithoutTable(): void
	{
		$this->createArticle('panel-grid-a', 'Panel Grid A');
		$this->createArticle('panel-grid-b', 'Panel Grid B');
		$response = $this->makeRequest('GET', '/panel/collection/test-articles');

		$this->assertResponseOk($response);
		$html = $this->getHtmlResponse($response);
		$this->assertStringContainsString('class="collection-title">Test articles', $html);
		$this->assertStringContainsString('Panel Grid A', $html);
		$this->assertStringContainsString('Panel Grid B', $html);
		$this->assertStringContainsString('class="collection-list"', $html);
		$this->assertStringContainsString('class="collection-grid"', $html);
		$this->assertStringNotContainsString('<table', $html);
	}

	public function testPanelCollectionRouteReturnsNotFoundForUnknownCollection(): void
	{
		$response = $this->makeRequest('GET', '/panel/collection/does-not-exist');

		$this->assertResponseStatus(404, $response);
	}

	private function createArticle(string $uid, string $title): void
	{
		$this->createTestNode([
			'uid' => $uid,
			'type' => $this->articleTypeId(),
			'published' => true,
			'content' => [
				'title' => [
					'type' => 'text',
					'value' => ['en' => $title],
				],
			],
		]);
	}

	private function articleTypeId(): int
	{
		if ($this->articleTypeId !== null) {
			return $this->articleTypeId;
		}

		$type = $this->db()->execute(
			"SELECT type FROM cms.types WHERE handle = 'test-article'",
		)->one();
		$this->assertNotEmpty($type);
		$this->articleTypeId = (int) $type['type'];

		return $this->articleTypeId;
	}
}
