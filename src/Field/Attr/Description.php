<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Description
{
	public function __construct(public readonly string $description) {}
}
