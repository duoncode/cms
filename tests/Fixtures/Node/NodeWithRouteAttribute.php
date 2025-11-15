<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Node\Meta\Route;
use Duon\Cms\Node\Page;

#[Route('/node-with-custom/{route}')]
class NodeWithRouteAttribute extends Page
{
	public function title(): string
	{
		return 'with route';
	}
}
