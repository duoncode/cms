<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\E2E;

use Duon\Cms\Tests\E2ETestCase;

/**
 * End-to-end tests for URL routing and catchall behavior.
 *
 * Tests how the CMS resolves URLs to nodes and handles 404s.
 *
 * @internal
 *
 * @coversNothing
 */
final class RoutingTest extends E2ETestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Load test data fixtures
		$this->loadFixtures('basic-types', 'sample-nodes');
	}

	public function testHomepageResolution(): void
	{
		// Act: Request homepage
		$response = $this->makeRequest('GET', '/');

		// Assert: Homepage is accessible
		$this->assertResponseOk($response);

		$html = $this->getHtmlResponse($response);
		$this->assertNotEmpty($html);
	}

	public function testPagePathResolution(): void
	{
		// Arrange: Create a test page with a path
		$typeId = $this->createTestType('routing-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'routing-test-page',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Test Page']],
				'path' => ['type' => 'text', 'value' => ['en' => '/about/team']],
			],
		]);

		// Act: Request the page by its path
		$response = $this->makeRequest('GET', '/about/team');

		// Assert: Page is resolved
		$this->assertResponseOk($response);

		$html = $this->getHtmlResponse($response);
		$this->assertNotEmpty($html);
	}

	public function testNestedPagePath(): void
	{
		// Arrange: Create parent and child pages
		$typeId = $this->createTestType('nested-test-page', 'page');

		$parentId = $this->createTestNode([
			'uid' => 'parent-page',
			'type' => $typeId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Parent']],
				'path' => ['type' => 'text', 'value' => ['en' => '/parent']],
			],
		]);

		$childId = $this->createTestNode([
			'uid' => 'child-page',
			'type' => $typeId,
			'parent' => $parentId,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Child']],
				'path' => ['type' => 'text', 'value' => ['en' => '/parent/child']],
			],
		]);

		// Act: Request the nested page
		$response = $this->makeRequest('GET', '/parent/child');

		// Assert: Nested page is resolved
		$this->assertResponseOk($response);

		$html = $this->getHtmlResponse($response);
		$this->assertNotEmpty($html);
	}

	public function test404ForNonExistentPath(): void
	{
		// Act: Request a path that doesn't exist
		$response = $this->makeRequest('GET', '/this/path/does/not/exist');

		// Assert: Returns 404 status
		$this->assertResponseStatus(404, $response);
	}

	public function test404ForNonExistentNode(): void
	{
		// Act: Request a non-existent node ID
		$response = $this->makeRequest('GET', '/node/99999999');

		// Assert: Returns 404 status
		$this->assertResponseStatus(404, $response);
	}

	public function testCatchallRouteMatchesAllPaths(): void
	{
		// The catchall route should handle any path that doesn't match other routes

		// Act: Request various paths
		$paths = [
			'/some/random/path',
			'/another-path',
			'/deeply/nested/path/here',
		];

		foreach ($paths as $path) {
			$response = $this->makeRequest('GET', $path);

			// Assert: Catchall handles it (returns response, even if 404)
			$this->assertNotNull($response);
			$statusCode = $response->getStatusCode();

			// Should get either 200 (if path matches a node) or 404 (if not found)
			$this->assertTrue(
				$statusCode === 200 || $statusCode === 404,
				"Expected 200 or 404, got {$statusCode} for path: {$path}"
			);
		}
	}

	public function testResponseHeaders(): void
	{
		// Act: Request homepage
		$response = $this->makeRequest('GET', '/');

		// Assert: Has appropriate headers
		$this->assertResponseHasHeader('Content-Type', $response);

		// Content-Type should be text/html for page responses
		$contentType = $response->getHeaderLine('Content-Type');
		$this->assertStringContainsString('text/html', $contentType);
	}

	public function testHiddenNodesAreNotAccessible(): void
	{
		// Arrange: Create a hidden node
		$typeId = $this->createTestType('hidden-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'hidden-page',
			'type' => $typeId,
			'hidden' => true,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Hidden Page']],
				'path' => ['type' => 'text', 'value' => ['en' => '/hidden-page']],
			],
		]);

		// Act: Try to access the hidden page
		$response = $this->makeRequest('GET', '/hidden-page');

		// Assert: Should return 404 (hidden pages are not publicly accessible)
		$this->assertResponseStatus(404, $response);
	}

	public function testUnpublishedNodesAreNotAccessible(): void
	{
		// Arrange: Create an unpublished node
		$typeId = $this->createTestType('unpublished-test-page', 'page');
		$nodeId = $this->createTestNode([
			'uid' => 'unpublished-page',
			'type' => $typeId,
			'published' => false,
			'content' => [
				'title' => ['type' => 'text', 'value' => ['en' => 'Unpublished Page']],
				'path' => ['type' => 'text', 'value' => ['en' => '/unpublished-page']],
			],
		]);

		// Act: Try to access the unpublished page
		$response = $this->makeRequest('GET', '/unpublished-page');

		// Assert: Should return 404 (unpublished pages are not publicly accessible)
		$this->assertResponseStatus(404, $response);
	}
}
