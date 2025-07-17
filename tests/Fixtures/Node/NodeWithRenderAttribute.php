<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Node\Attr\Render;
use Duon\Cms\Node\Page;

#[Render('template-defined-by-render-attribute')]
class NodeWithRenderAttribute extends Page
{
	public function title(): string
	{
		return 'with render';
	}
}
