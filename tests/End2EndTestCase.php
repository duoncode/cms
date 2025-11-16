<?php

declare(strict_types=1);

namespace Duon\Cms\Tests;

use Duon\Cms\Cms;
use Duon\Core\App;
use Duon\Core\Factory\Laminas;
use Duon\Registry\Registry;
use Duon\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Base class for end-to-end tests that test the full HTTP request/response cycle.
 *
 * This class extends IntegrationTestCase and adds application setup with routing,
 * middleware, and CMS integration. Tests run through the full stack using in-process
 * HTTP requests (no external server needed).
 *
 * @internal
 *
 * @coversNothing
 */
class End2EndTestCase extends IntegrationTestCase
{
	protected App $app;

	protected function setUp(): void
	{
		parent::setUp();
		$this->app = $this->createApp();
	}

	/**
	 * Create and configure the application for End2End testing.
	 */
	protected function createApp(): App
	{
		$factory = new Laminas();
		$router = new Router();
		$registry = $this->registry();
		$config = $this->config([
			'db.dsn' => 'pgsql:host=localhost;dbname=duoncms;user=duoncms;password=duoncms',
			'path.root' => self::root(),
			'path.public' => self::root() . '/public',
			'path.uploads' => self::root() . '/public/uploads',
			'upload.maxSize' => 10 * 1024 * 1024, // 10MB
			'upload.allowedExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
		]);

		$app = new App($factory, $router, $registry, $config);

		// Load CMS
		$cms = $this->createCms();
		$app->load($cms);
		$app->addRoute($cms->catchallRoute());

		return $app;
	}

	/**
	 * Create and configure the CMS instance for testing.
	 */
	protected function createCms(): Cms
	{
		$cms = new Cms(sessionEnabled: false);

		// Register test nodes from fixtures
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestPage::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestArticle::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestHome::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestBlock::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestWidget::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestDocument::class);
		$cms->node(\Duon\Cms\Tests\Fixtures\Node\TestMediaDocument::class);

		return $cms;
	}

	/**
	 * Make an HTTP request through the application.
	 *
	 * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
	 * @param string $uri Request URI
	 * @param array $options Request options:
	 *   - 'headers' => array - HTTP headers
	 *   - 'body' => string|array - Request body (array will be JSON encoded)
	 *   - 'query' => array - Query parameters
	 *   - 'cookies' => array - Cookie values
	 * @return ResponseInterface PSR-7 response
	 */
	protected function makeRequest(string $method, string $uri, array $options = []): ResponseInterface
	{
		$psrRequest = $this->factory()->serverRequest($method, $uri);

		// Add query parameters
		if (isset($options['query'])) {
			$queryString = http_build_query($options['query']);
			$uriObj = $psrRequest->getUri()->withQuery($queryString);
			$psrRequest = $psrRequest->withUri($uriObj);
		}

		// Add headers
		if (isset($options['headers'])) {
			foreach ($options['headers'] as $name => $value) {
				$psrRequest = $psrRequest->withHeader($name, $value);
			}
		}

		// Add cookies
		if (isset($options['cookies'])) {
			$cookieHeader = [];
			foreach ($options['cookies'] as $name => $value) {
				$cookieHeader[] = "{$name}={$value}";
			}
			$psrRequest = $psrRequest->withHeader('Cookie', implode('; ', $cookieHeader));
		}

		// Add body
		if (isset($options['body'])) {
			$body = $options['body'];

			if (is_array($body)) {
				$body = json_encode($body);
				$psrRequest = $psrRequest->withHeader('Content-Type', 'application/json');
			}

			$stream = $this->factory()->streamFactory()->createStream($body);
			$psrRequest = $psrRequest->withBody($stream);
		}

		// Capture output and return response without emitting
		ob_start();
		$response = $this->app->run($psrRequest);
		ob_end_clean();

		return $response;
	}

	/**
	 * Assert that the response has the expected status code.
	 */
	protected function assertResponseStatus(int $expected, ResponseInterface $response, string $message = ''): void
	{
		$this->assertEquals(
			$expected,
			$response->getStatusCode(),
			$message ?: "Expected status code {$expected}, got {$response->getStatusCode()}"
		);
	}

	/**
	 * Assert that the response has a successful status code (2xx).
	 */
	protected function assertResponseOk(ResponseInterface $response): void
	{
		$statusCode = $response->getStatusCode();
		$this->assertGreaterThanOrEqual(200, $statusCode, 'Expected successful response');
		$this->assertLessThan(300, $statusCode, 'Expected successful response');
	}

	/**
	 * Get the response body as a decoded JSON array.
	 *
	 * @return array
	 */
	protected function getJsonResponse(ResponseInterface $response): array
	{
		$body = (string) $response->getBody();
		$decoded = json_decode($body, true);

		$this->assertIsArray($decoded, 'Response body is not valid JSON');

		return $decoded;
	}

	/**
	 * Get the response body as HTML string.
	 */
	protected function getHtmlResponse(ResponseInterface $response): string
	{
		return (string) $response->getBody();
	}

	/**
	 * Assert that response contains specific header.
	 */
	protected function assertResponseHasHeader(string $header, ResponseInterface $response): void
	{
		$this->assertTrue(
			$response->hasHeader($header),
			"Response does not have header: {$header}"
		);
	}

	/**
	 * Assert that response header has expected value.
	 */
	protected function assertResponseHeaderEquals(string $header, string $expected, ResponseInterface $response): void
	{
		$this->assertResponseHasHeader($header, $response);
		$actual = $response->getHeaderLine($header);
		$this->assertEquals($expected, $actual, "Header {$header} has unexpected value");
	}
}