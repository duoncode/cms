<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Meta;

use Attribute;

#[Attribute]
class Render
{
	public function __construct(public readonly string $value) {}
}
