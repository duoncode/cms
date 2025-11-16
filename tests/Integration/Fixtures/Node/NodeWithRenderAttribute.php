<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration\Fixtures\Node;

use Duon\Cms\Node\Meta\Render;
use Duon\Cms\Node\Page;

#[Render('template-defined-by-render-attribute')]
class NodeWithRenderAttribute extends Page
{
	public function title(): string
	{
		return 'with render';
	}
}
