<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit\Boiler\Error;

use Duon\Cms\Boiler\Error\WhoopsHandler;
use Duon\Cms\Tests\TestCase;
use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class WhoopsHandlerTest extends TestCase
{
	public function testHandleReturns500Response(): void
	{
		$handler = new WhoopsHandler();
		$exception = new Exception('Test error');

		$response = $handler->handle($exception, $this->factory()->responseFactory());

		$this->assertInstanceOf(ResponseInterface::class, $response);
		$this->assertSame(500, $response->getStatusCode());
		$this->assertSame('text/html', $response->getHeaderLine('Content-type'));
	}
}
