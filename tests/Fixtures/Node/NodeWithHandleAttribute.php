<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Node\Meta\Handle;
use Duon\Cms\Node\Page;

#[Handle('node-with-custom-handle-attribute')]
class NodeWithHandleAttribute extends Page
{
	public function title(): string
	{
		return 'with handle';
	}
}
