<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Fulltext extends Capability
{
	public function __construct(public readonly FulltextWeight $fulltextWeight) {}

	public function capabilities(): int
	{
		return Field::CAPABILITY_FULLTEXT;
	}
}
