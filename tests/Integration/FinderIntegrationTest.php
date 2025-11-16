<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration;

use Duon\Cms\Tests\IntegrationTestCase;

final class FinderIntegrationTest extends IntegrationTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Load fixtures for all tests
		$this->loadFixtures('basic-types', 'sample-nodes');
	}

	public function testFinderReturnsNodesOfSpecificType(): void
	{
		// Act
		$finder = $this->createFinder();
		$nodes = $finder->nodes->types('test-article');

		// Assert
		$this->assertNotEmpty($nodes);

		// Verify all returned nodes are of the correct type
		foreach ($nodes as $node) {
			$this->assertEquals('test-article', $node::handle());
		}
	}

	public function testFinderFiltersPublishedNodes(): void
	{
		// Act
		$finder = $this->createFinder();
		$publishedNodes = iterator_to_array($finder->nodes->types('test-article')->published(true));
		$allNodes = iterator_to_array($finder->nodes->types('test-article')->published(null));

		// Assert
		$this->assertNotEmpty($publishedNodes);
		$this->assertGreaterThan(count($publishedNodes), count($allNodes));

		// Verify all returned nodes are published
		foreach ($publishedNodes as $node) {
			$this->assertTrue($node->data()['published']);
		}
	}

	public function testFinderFiltersUnpublishedNodes(): void
	{
		// Act
		$finder = $this->createFinder();
		$unpublishedNodes = $finder->nodes()
			->types('test-article')
			->published(false);

		// Assert
		$this->assertNotEmpty($unpublishedNodes);

		// Verify all returned nodes are unpublished
		foreach ($unpublishedNodes as $node) {
			$this->assertFalse($node->data()['published']);
		}
	}

	public function testFinderSupportsMultipleTypes(): void
	{
		// Act
		$finder = $this->createFinder();
		$nodes = $finder->nodes()
			->types('test-page', 'test-article');

		// Assert
		$this->assertNotEmpty($nodes);

		// Get all type handles for returned nodes
		$typeHandles = [];

		foreach ($nodes as $node) {
			$typeHandles[] = $node::handle();
		}

		// Verify we got both types
		$uniqueTypes = array_unique($typeHandles);
		$this->assertContains('test-page', $uniqueTypes);
		$this->assertContains('test-article', $uniqueTypes);
	}

	public function testFinderOrdersByField(): void
	{
		// Arrange - create nodes with different creation times
		$typeId = $this->createTestType('ordered-test-page', 'page');

		// Create nodes with specific UIDs to ensure predictable ordering
		$this->createTestNode([
			'uid' => 'ordered-c',
			'type' => $typeId,
			'content' => ['title' => ['type' => 'text', 'value' => ['en' => 'C Title']]],
		]);
		$this->createTestNode([
			'uid' => 'ordered-a',
			'type' => $typeId,
			'content' => ['title' => ['type' => 'text', 'value' => ['en' => 'A Title']]],
		]);
		$this->createTestNode([
			'uid' => 'ordered-b',
			'type' => $typeId,
			'content' => ['title' => ['type' => 'text', 'value' => ['en' => 'B Title']]],
		]);

		// Act - order by UID ascending
		$finder = $this->createFinder();
		$nodes = iterator_to_array($finder->nodes()
			->types('ordered-test-page')
			->order('uid ASC'));

		// Assert
		$this->assertCount(3, $nodes);
		$this->assertEquals('ordered-a', $nodes[0]->uid());
		$this->assertEquals('ordered-b', $nodes[1]->uid());
		$this->assertEquals('ordered-c', $nodes[2]->uid());
	}

	public function testFinderLimitsResults(): void
	{
		// Arrange - create multiple nodes
		$typeId = $this->createTestType('limit-test-page', 'page');

		for ($i = 1; $i <= 5; $i++) {
			$this->createTestNode([
				'uid' => "limit-node-{$i}",
				'type' => $typeId,
			]);
		}

		// Act
		$finder = $this->createFinder();
		$nodes = iterator_to_array($finder->nodes()
			->types('limit-test-page')
			->limit(3));

		// Assert
		$this->assertCount(3, $nodes);
	}

	public function testFinderFiltersHiddenNodes(): void
	{
		// Arrange
		$typeId = $this->createTestType('hidden-test-page', 'page');

		$this->createTestNode([
			'uid' => 'visible-node',
			'type' => $typeId,
			'hidden' => false,
		]);

		$this->createTestNode([
			'uid' => 'hidden-node',
			'type' => $typeId,
			'hidden' => true,
		]);

		// Act
		$finder = $this->createFinder();
		$visibleNodes = iterator_to_array($finder->nodes()
			->types('hidden-test-page')
			->hidden(false));

		// Assert
		$this->assertCount(1, $visibleNodes);
		$this->assertEquals('visible-node', $visibleNodes[0]->uid());
	}

	public function testFinderReturnsEmptyArrayWhenNoResults(): void
	{
		// Act
		$finder = $this->createFinder();
		$nodes = iterator_to_array($finder->nodes()
			->types('non-existent-type'));

		// Assert
		$this->assertIsArray($nodes);
		$this->assertEmpty($nodes);
	}

	public function testFinderWithFixtureData(): void
	{
		// This test verifies that the sample-nodes fixture was loaded correctly

		// Act
		$finder = $this->createFinder();
		$homepage = $finder->nodes()->types('test-page');

		// Assert
		$this->assertNotEmpty($homepage);

		// Find the test-homepage node
		$homepageNode = null;

		foreach ($homepage as $node) {
			if ($node->uid() === 'test-homepage') {
				$homepageNode = $node;
				break;
			}
		}

		$this->assertNotNull($homepageNode, 'test-homepage node should exist');
		$this->assertTrue($homepageNode->data()['published']);

		// Verify content structure
		$content = $homepageNode->data()['content'];
		$this->assertArrayHasKey('title', $content);
		$this->assertEquals('Testhomepage', $content['title']['value']['de']);
		$this->assertEquals('Test Homepage', $content['title']['value']['en']);
	}
}
