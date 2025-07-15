<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Attr;

use Attribute;

#[Attribute]
class Template
{
	public function __construct(public readonly string $value) {}
}
