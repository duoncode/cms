<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TranslateFile extends Capability
{
	public function capabilities(): int
	{
		return Field::CAPABILITY_TRANSLATE_FILE;
	}
}
