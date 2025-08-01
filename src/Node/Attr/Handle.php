<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Attr;

use Attribute;

#[Attribute]
class Handle
{
	public function __construct(public readonly string $value) {}
}
