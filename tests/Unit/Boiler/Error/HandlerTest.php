<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit\Boiler\Error;

use Duon\Cms\Boiler\Error\Handler;
use Duon\Cms\Tests\TestCase;
use Duon\Error\Handler as ErrorHandler;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Psr\Log\NullLogger;

/**
 * @internal
 *
 * @coversNothing
 */
final class HandlerTest extends TestCase
{
	public function testViewsMethodReturnsInstance(): void
	{
		$handler = new Handler(
			root: self::root(),
			logger: new NullLogger(),
			factory: $this->factory(),
		);

		$result = $handler->views('tests/Fixtures/Boiler/templates');

		$this->assertSame($handler, $result);
	}

	public function testTrustedMergesByDefault(): void
	{
		$handler = new Handler(
			root: self::root(),
			logger: new NullLogger(),
			factory: $this->factory(),
		);

		$result = $handler->trusted([self::class]);

		$this->assertSame($handler, $result);
	}

	public function testTrustedCanReplace(): void
	{
		$handler = new Handler(
			root: self::root(),
			logger: new NullLogger(),
			factory: $this->factory(),
		);

		$result = $handler->trusted([self::class], replace: true);

		$this->assertSame($handler, $result);
	}

	#[RunInSeparateProcess]
	public function testCreateReturnsErrorHandler(): void
	{
		$_ENV['CMS_DEBUG'] = 'false';
		$_ENV['CMS_ENV'] = 'test';

		$handler = new Handler(
			root: self::root(),
			logger: new NullLogger(),
			factory: $this->factory(),
		);

		$handler->views('tests/Fixtures/Boiler/templates');
		$errorHandler = $handler->create();

		$this->assertInstanceOf(ErrorHandler::class, $errorHandler);
		$errorHandler->restoreHandlers();
	}

	#[RunInSeparateProcess]
	public function testCreateWithDebugMode(): void
	{
		$_ENV['CMS_DEBUG'] = 'true';
		$_ENV['CMS_ENV'] = 'test';

		$handler = new Handler(
			root: self::root(),
			logger: new NullLogger(),
			factory: $this->factory(),
		);

		$handler->views('tests/Fixtures/Boiler/templates');
		$errorHandler = $handler->create();

		$this->assertInstanceOf(ErrorHandler::class, $errorHandler);
		$errorHandler->restoreHandlers();
	}
}
