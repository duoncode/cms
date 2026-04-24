<?php

declare(strict_types=1);

namespace Duon\Cms\Boiler\Error;

use Duon\Error\DebugHandler;
use Override;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

final class WhoopsHandler implements DebugHandler
{
	#[Override]
	public function handle(Throwable $exception, ResponseFactory $factory): Response
	{
		$whoops = new \Whoops\Run();
		$whoops->allowQuit(false);
		$whoops->writeToOutput(false);
		$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

		$response = $factory->createResponse()->withStatus(500)->withHeader('Content-type', 'text/html');
		$response->getBody()->write($whoops->handleException($exception));

		return $response;
	}
}
