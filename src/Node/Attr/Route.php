<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Attr;

use Attribute;

#[Attribute]
class Route
{
	public function __construct(private array|string $routePattern) {}
}
