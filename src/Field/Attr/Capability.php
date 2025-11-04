<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Duon\Cms\Field\Field;

interface Capability
{
	public function set(Field $field): void;
}
