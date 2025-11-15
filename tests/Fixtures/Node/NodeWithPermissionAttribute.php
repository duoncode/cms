<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Node\Meta\Permission;
use Duon\Cms\Node\Page;

#[Permission([
	'read' => 'me',
])]
class NodeWithPermissionAttribute extends Page
{
	public function title(): string
	{
		return 'with permission';
	}
}
