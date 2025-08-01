<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Node\Attr\Name;
use Duon\Cms\Node\Page;

#[Name('Node With Custom Name Attribute')]
class NodeWithNameAttribute extends Page
{
	public function title(): string
	{
		return 'with name';
	}
}
