<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;
use Duon\Cms\Field\Field;
use Duon\Cms\Field\Capability\FileTranslatable;
use Duon\Cms\Exception\RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TranslateFile implements Capability
{
	public function set(Field $field): void
	{
		if ($field instanceof FileTranslatable) {
			$field->translateFile(true);

			return;
		}

		throw new RuntimeException("The field " . $field::class . " does not have the capability " . FileTranslatable::class);
	}
}
