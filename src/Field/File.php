<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Value;

class File extends Field implements Capability\Translatable, Capability\FileTranslatable, Capability\AllowsMultiple
{
	use Capability\DoesAllowMultiple;
	use Capability\IsTranslatable;
	use Capability\FileIsTranslatable;

	public const EXTRA_CAPABILITIES = Field::CAPABILITY_MULTIPLE | Field::CAPABILITY_TRANSLATE_FILE;

	public function value(): Value\File|Value\Files
	{
		if ($this->multiple) {
			if ($this->translateFile) {
				return new Value\TranslatedFiles($this->node, $this, $this->valueContext);
			}

			return new Value\Files($this->node, $this, $this->valueContext);
		}

		if ($this->translateFile) {
			return new Value\TranslatedFile($this->node, $this, $this->valueContext);
		}

		return new Value\File($this->node, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		if ($this->translateFile) {
			return $this->getTranslatableFileStructure('file', $value);
		}

		return $this->getFileStructure('file', $value);
	}

	public function properties(): array
	{
		return array_merge(parent::properties(), [
			'multiple' => $this->multiple,
			'translateFile' => $this->translateFile,
		]);
	}
}
