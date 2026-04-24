<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit\Boiler\Error;

use Duon\Cms\Boiler\Error\Renderer;
use Duon\Cms\Boiler\Error\RendererFactory;
use Duon\Cms\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RendererFactoryTest extends TestCase
{
	public function testWithTemplateCreatesRenderer(): void
	{
		$factory = new RendererFactory(
			dirs: [self::root() . '/tests/Fixtures/Boiler/templates'],
			context: ['foo' => 'bar'],
			trusted: [],
			autoescape: true,
		);

		$renderer = $factory->withTemplate('error');

		$this->assertInstanceOf(Renderer::class, $renderer);
	}
}
