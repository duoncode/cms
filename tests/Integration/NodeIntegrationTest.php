<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration;

use Duon\Cms\Tests\IntegrationTestCase;

final class NodeIntegrationTest extends IntegrationTestCase
{
	public function testCreateAndRetrieveNode(): void
	{
		// Arrange
		$typeId = $this->createTestType('integration-test-page', 'page');
		$content = [
			'title' => [
				'type' => 'text',
				'value' => ['de' => 'Testseite', 'en' => 'Test Page'],
			],
			'body' => [
				'type' => 'html',
				'value' => ['de' => '<p>Deutscher Inhalt</p>', 'en' => '<p>English content</p>'],
			],
		];

		// Act
		$nodeId = $this->createTestNode([
			'uid' => 'integration-test-node-1',
			'type' => $typeId,
			'content' => $content,
		]);

		// Assert
		$node = $this->db()->execute(
			'SELECT * FROM cms.nodes WHERE node = :id',
			['id' => $nodeId]
		)->one();

		$this->assertNotNull($node);
		$this->assertEquals('integration-test-node-1', $node['uid']);
		$this->assertEquals($typeId, $node['type']);
		$this->assertTrue($node['published']);
		$this->assertFalse($node['hidden']);

		// Verify content structure
		$contentData = json_decode($node['content'], true);
		$this->assertEquals('Testseite', $contentData['title']['value']['de']);
		$this->assertEquals('Test Page', $contentData['title']['value']['en']);
	}

	public function testCreateNodeWithDefaults(): void
	{
		// Arrange
		$typeId = $this->createTestType('default-test-page', 'page');

		// Act - create node with minimal data
		$nodeId = $this->createTestNode([
			'type' => $typeId,
		]);

		// Assert
		$node = $this->db()->execute(
			'SELECT * FROM cms.nodes WHERE node = :id',
			['id' => $nodeId]
		)->one();

		$this->assertNotNull($node);
		$this->assertTrue($node['published']); // Default is true
		$this->assertFalse($node['hidden']); // Default is false
		$this->assertFalse($node['locked']); // Default is false
		$this->assertEquals(1, $node['creator']); // System user
		$this->assertEquals(1, $node['editor']); // System user
	}

	public function testUpdateNodeContent(): void
	{
		// Arrange
		$typeId = $this->createTestType('update-test-page', 'page');
		$initialContent = [
			'title' => ['type' => 'text', 'value' => ['en' => 'Initial Title']],
		];

		$nodeId = $this->createTestNode([
			'uid' => 'update-test-node',
			'type' => $typeId,
			'content' => $initialContent,
		]);

		// Act - update the node content
		$updatedContent = [
			'title' => ['type' => 'text', 'value' => ['en' => 'Updated Title']],
			'subtitle' => ['type' => 'text', 'value' => ['en' => 'New Subtitle']],
		];

		$this->db()->execute(
			'UPDATE cms.nodes SET content = :content::jsonb WHERE node = :id',
			['id' => $nodeId, 'content' => json_encode($updatedContent)]
		)->run();

		// Assert
		$node = $this->db()->execute(
			'SELECT content FROM cms.nodes WHERE node = :id',
			['id' => $nodeId]
		)->one();

		$contentData = json_decode($node['content'], true);
		$this->assertEquals('Updated Title', $contentData['title']['value']['en']);
		$this->assertEquals('New Subtitle', $contentData['subtitle']['value']['en']);
	}

	public function testQueryNodesByType(): void
	{
		// Arrange
		$typeId = $this->createTestType('query-test-page', 'page');

		// Create multiple nodes of the same type
		$this->createTestNode(['uid' => 'query-node-1', 'type' => $typeId, 'published' => true]);
		$this->createTestNode(['uid' => 'query-node-2', 'type' => $typeId, 'published' => true]);
		$this->createTestNode(['uid' => 'query-node-3', 'type' => $typeId, 'published' => false]);

		// Act
		$nodes = $this->db()->execute(
			'SELECT * FROM cms.nodes WHERE type = :type AND published = true ORDER BY node',
			['type' => $typeId]
		)->all();

		// Assert
		$this->assertCount(2, $nodes);
		$this->assertEquals('query-node-1', $nodes[0]['uid']);
		$this->assertEquals('query-node-2', $nodes[1]['uid']);
	}

	public function testNodeHierarchy(): void
	{
		// Arrange
		$typeId = $this->createTestType('hierarchy-test-page', 'page');

		// Create parent node
		$parentId = $this->createTestNode([
			'uid' => 'hierarchy-parent',
			'type' => $typeId,
		]);

		// Create child node
		$childId = $this->createTestNode([
			'uid' => 'hierarchy-child',
			'type' => $typeId,
			'parent' => $parentId,
		]);

		// Act - query child nodes of parent
		$children = $this->db()->execute(
			'SELECT * FROM cms.nodes WHERE parent = :parent',
			['parent' => $parentId]
		)->all();

		// Assert
		$this->assertCount(1, $children);
		$this->assertEquals('hierarchy-child', $children[0]['uid']);
		$this->assertEquals($parentId, $children[0]['parent']);
	}

	public function testDeleteNode(): void
	{
		// Arrange
		$typeId = $this->createTestType('delete-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'delete-test-node',
			'type' => $typeId,
		]);

		// Verify node exists
		$exists = $this->db()->execute(
			'SELECT EXISTS(SELECT 1 FROM cms.nodes WHERE node = :id) as exists',
			['id' => $nodeId]
		)->one()['exists'];
		$this->assertTrue($exists);

		// Act - delete the node
		$this->db()->execute(
			'DELETE FROM cms.nodes WHERE node = :id',
			['id' => $nodeId]
		)->run();

		// Assert - verify node is deleted
		$exists = $this->db()->execute(
			'SELECT EXISTS(SELECT 1 FROM cms.nodes WHERE node = :id) as exists',
			['id' => $nodeId]
		)->one()['exists'];
		$this->assertFalse($exists);
	}

	public function testNodeJsonbQuerying(): void
	{
		// Arrange
		$typeId = $this->createTestType('jsonb-test-page', 'page');

		$this->createTestNode([
			'uid' => 'jsonb-node-1',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'First Title']],
			],
		]);

		$this->createTestNode([
			'uid' => 'jsonb-node-2',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Second Title']],
			],
		]);

		// Act - query using JSONB path
		$nodes = $this->db()->execute(
			"SELECT uid, content->'title'->'value'->>'en' as title
			 FROM cms.nodes
			 WHERE type = :type
			 AND content->'title'->'value'->>'en' LIKE '%Second%'",
			['type' => $typeId]
		)->all();

		// Assert
		$this->assertCount(1, $nodes);
		$this->assertEquals('jsonb-node-2', $nodes[0]['uid']);
		$this->assertEquals('Second Title', $nodes[0]['title']);
	}
}
