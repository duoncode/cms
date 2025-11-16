<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Tests\Fixtures\Node\NodeWithHandleAttribute;
use Duon\Cms\Tests\Fixtures\Node\NodeWithNameAttribute;
use Duon\Cms\Tests\Fixtures\Node\NodeWithPermissionAttribute;
use Duon\Cms\Tests\Fixtures\Node\NodeWithRenderAttribute;
use Duon\Cms\Tests\Fixtures\Node\NodeWithRouteAttribute;
use Duon\Cms\Tests\TestCase;

final class NodeMetaTest extends TestCase
{
	public function testNameAttributeSet(): void
	{
		$this->assertEquals('NodeWithHandleAttribute', NodeWithHandleAttribute::name());
		$this->assertEquals('Node With Custom Name Attribute', NodeWithNameAttribute::name());
	}

	public function testHandleAttributeSet(): void
	{
		$this->assertEquals('node-with-name-attribute', NodeWithNameAttribute::handle());
		$this->assertEquals('node-with-custom-handle-attribute', NodeWithHandleAttribute::handle());
	}

	public function testRouteAttributeSet(): void
	{
		$this->assertEquals('', NodeWithNameAttribute::route());
		$this->assertEquals('/node-with-custom/{route}', NodeWithRouteAttribute::route());
	}

	public function testRenderAttributeSet(): void
	{
		$this->assertEquals(['template', 'node-with-name-attribute'], NodeWithNameAttribute::renderer());
		$this->assertEquals(['template', 'template-defined-by-render-attribute'], NodeWithRenderAttribute::renderer());
	}

	public function testPermissionAttributeSet(): void
	{
		$this->assertEquals([
			'read' => 'everyone',
			'create' => 'authenticated',
			'change' => 'authenticated',
			'deeete' => 'authenticated',
		], NodeWithNameAttribute::permission());
		$this->assertEquals([
			'read' => 'me',
		], NodeWithPermissionAttribute::permission());
	}
}
