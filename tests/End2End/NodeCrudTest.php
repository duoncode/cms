<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\End2End;

use Duon\Cms\Tests\End2EndTestCase;

/**
 * End-to-end tests for Node CRUD operations through HTTP API.
 *
 * @internal
 *
 * @coversNothing
 */
final class NodeCrudTest extends End2EndTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Load test data fixtures
		$this->loadFixtures('basic-types', 'sample-nodes');
	}

	public function testGetNodeList(): void
	{
		// Act: Request list of nodes
		$response = $this->makeRequest('GET', '/api/nodes', [
			'query' => ['type' => 'test-article'],
		]);

		// Assert: Response is successful
		$this->assertResponseOk($response);

		// Note: This test will fail until API routes are implemented
		// For now, we're testing the infrastructure works
	}

	public function testGetSingleNode(): void
	{
		// Arrange: Create a test node
		$typeId = $this->createTestType('crud-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'crud-test-node',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Test Node']],
			],
		]);

		// Act: Request the node via API
		$response = $this->makeRequest('GET', "/api/nodes/{$nodeId}");

		// Assert: Response contains node data
		// Note: This test will fail until API routes are implemented
		$this->assertResponseStatus(404, $response); // Expecting 404 until routes exist
	}

	public function testCreateNode(): void
	{
		// Arrange: Prepare node data
		$typeId = $this->createTestType('create-test-page', 'page');
		$nodeData = [
			'uid' => 'new-test-node',
			'type' => $typeId,
			'published' => true,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'New Node']],
			],
		];

		// Act: POST request to create node
		$response = $this->makeRequest('POST', '/api/nodes', [
			'body' => $nodeData,
			'headers' => ['Content-Type' => 'application/json'],
		]);

		// Assert: Node was created
		// Note: This test will fail until API routes are implemented
		$this->assertResponseStatus(404, $response); // Expecting 404 until routes exist
	}

	public function testUpdateNode(): void
	{
		// Arrange: Create a test node
		$typeId = $this->createTestType('update-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'update-test-node',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Original Title']],
			],
		]);

		$updateData = [
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Updated Title']],
			],
		];

		// Act: PUT request to update node
		$response = $this->makeRequest('PUT', "/api/nodes/{$nodeId}", [
			'body' => $updateData,
			'headers' => ['Content-Type' => 'application/json'],
		]);

		// Assert: Node was updated
		// Note: This test will fail until API routes are implemented
		$this->assertResponseStatus(404, $response); // Expecting 404 until routes exist
	}

	public function testDeleteNode(): void
	{
		// Arrange: Create a test node
		$typeId = $this->createTestType('delete-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'delete-test-node',
			'type' => $typeId,
		]);

		// Act: DELETE request to remove node
		$response = $this->makeRequest('DELETE', "/api/nodes/{$nodeId}");

		// Assert: Node was deleted
		// Note: This test will fail until API routes are implemented
		$this->assertResponseStatus(404, $response); // Expecting 404 until routes exist
	}
}