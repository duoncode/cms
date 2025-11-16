<?php

declare(strict_types=1);

namespace Duon\Cms\Tests;

use Duon\Cms\Tests\Setup\IntegrationTestCase;

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
		$nodes = $finder->nodes()
			->types('test-article')
			->get();

		// Assert
		$this->assertNotEmpty($nodes);

		// Verify all returned nodes are of the correct type
		foreach ($nodes as $node) {
			$type = $this->db()->execute(
				'SELECT handle FROM cms.types WHERE type = :type',
				['type' => $node['type']]
			)->one();
			$this->assertEquals('test-article', $type['handle']);
		}
	}

	public function testFinderFiltersPublishedNodes(): void
	{
		// Act
		$finder = $this->createFinder();
		$publishedNodes = $finder->nodes()
			->types('test-article')
			->published(true)
			->get();

		$allNodes = $finder->nodes()
			->types('test-article')
			->get();

		// Assert
		$this->assertNotEmpty($publishedNodes);
		$this->assertGreaterThan(count($publishedNodes), count($allNodes));

		// Verify all returned nodes are published
		foreach ($publishedNodes as $node) {
			$this->assertTrue($node['published']);
		}
	}

	public function testFinderFiltersUnpublishedNodes(): void
	{
		// Act
		$finder = $this->createFinder();
		$unpublishedNodes = $finder->nodes()
			->types('test-article')
			->published(false)
			->get();

		// Assert
		$this->assertNotEmpty($unpublishedNodes);

		// Verify all returned nodes are unpublished
		foreach ($unpublishedNodes as $node) {
			$this->assertFalse($node['published']);
		}
	}

	public function testFinderSupportsMultipleTypes(): void
	{
		// Act
		$finder = $this->createFinder();
		$nodes = $finder->nodes()
			->types('test-page', 'test-article')
			->get();

		// Assert
		$this->assertNotEmpty($nodes);

		// Get all type handles for returned nodes
		$typeHandles = [];
		foreach ($nodes as $node) {
			$type = $this->db()->execute(
				'SELECT handle FROM cms.types WHERE type = :type',
				['type' => $node['type']]
			)->one();
			$typeHandles[] = $type['handle'];
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
		$nodes = $finder->nodes()
			->types('ordered-test-page')
			->order('uid ASC')
			->get();

		// Assert
		$this->assertCount(3, $nodes);
		$this->assertEquals('ordered-a', $nodes[0]['uid']);
		$this->assertEquals('ordered-b', $nodes[1]['uid']);
		$this->assertEquals('ordered-c', $nodes[2]['uid']);
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
		$nodes = $finder->nodes()
			->types('limit-test-page')
			->limit(3)
			->get();

		// Assert
		$this->assertCount(3, $nodes);
	}

	public function testFinderOffsetResults(): void
	{
		// Arrange - create multiple nodes
		$typeId = $this->createTestType('offset-test-page', 'page');

		for ($i = 1; $i <= 5; $i++) {
			$this->createTestNode([
				'uid' => "offset-node-{$i}",
				'type' => $typeId,
			]);
		}

		// Act
		$finder = $this->createFinder();
		$nodesWithOffset = $finder->nodes()
			->types('offset-test-page')
			->order('uid ASC')
			->offset(2)
			->get();

		// Assert
		$this->assertCount(3, $nodesWithOffset); // 5 total - 2 offset = 3
		$this->assertEquals('offset-node-3', $nodesWithOffset[0]['uid']);
	}

	public function testFinderCombinesLimitAndOffset(): void
	{
		// Arrange - create multiple nodes
		$typeId = $this->createTestType('pagination-test-page', 'page');

		for ($i = 1; $i <= 10; $i++) {
			$this->createTestNode([
				'uid' => sprintf('pagination-node-%02d', $i),
				'type' => $typeId,
			]);
		}

		// Act - get page 2 with page size 3
		$finder = $this->createFinder();
		$page2 = $finder->nodes()
			->types('pagination-test-page')
			->order('uid ASC')
			->offset(3)
			->limit(3)
			->get();

		// Assert
		$this->assertCount(3, $page2);
		$this->assertEquals('pagination-node-04', $page2[0]['uid']);
		$this->assertEquals('pagination-node-05', $page2[1]['uid']);
		$this->assertEquals('pagination-node-06', $page2[2]['uid']);
	}

	public function testFinderCountsResults(): void
	{
		// Arrange
		$typeId = $this->createTestType('count-test-page', 'page');

		for ($i = 1; $i <= 7; $i++) {
			$this->createTestNode([
				'uid' => "count-node-{$i}",
				'type' => $typeId,
				'published' => $i <= 5, // First 5 published, last 2 unpublished
			]);
		}

		// Act
		$finder = $this->createFinder();
		$totalCount = $finder->nodes()
			->types('count-test-page')
			->count();

		$publishedCount = $finder->nodes()
			->types('count-test-page')
			->published(true)
			->count();

		// Assert
		$this->assertEquals(7, $totalCount);
		$this->assertEquals(5, $publishedCount);
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
		$visibleNodes = $finder->nodes()
			->types('hidden-test-page')
			->hidden(false)
			->get();

		// Assert
		$this->assertCount(1, $visibleNodes);
		$this->assertEquals('visible-node', $visibleNodes[0]['uid']);
	}

	public function testFinderReturnsEmptyArrayWhenNoResults(): void
	{
		// Act
		$finder = $this->createFinder();
		$nodes = $finder->nodes()
			->types('non-existent-type')
			->get();

		// Assert
		$this->assertIsArray($nodes);
		$this->assertEmpty($nodes);
	}

	public function testFinderWithFixtureData(): void
	{
		// This test verifies that the sample-nodes fixture was loaded correctly

		// Act
		$finder = $this->createFinder();
		$homepage = $finder->nodes()
			->types('test-page')
			->get();

		// Assert
		$this->assertNotEmpty($homepage);

		// Find the test-homepage node
		$homepageNode = null;
		foreach ($homepage as $node) {
			if ($node['uid'] === 'test-homepage') {
				$homepageNode = $node;
				break;
			}
		}

		$this->assertNotNull($homepageNode, 'test-homepage node should exist');
		$this->assertTrue($homepageNode['published']);

		// Verify content structure
		$content = json_decode($homepageNode['content'], true);
		$this->assertArrayHasKey('title', $content);
		$this->assertEquals('Testhomepage', $content['title']['value']['de']);
		$this->assertEquals('Test Homepage', $content['title']['value']['en']);
	}
}
