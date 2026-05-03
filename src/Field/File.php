<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Validation\Shape as ValidationShape;
use Duon\Cms\Value;
use Duon\Sire\Shape;

class File extends Field implements
	Capability\Limitable,
	Capability\File\Translatable,
	Capability\Translatable
{
	use Capability\IsLimitable;
	use Capability\IsTranslatable;
	use Capability\File\IsTranslatable;

	public function value(): Value\File|Value\Files
	{
		if ($this->allowsMultipleItems()) {
			if ($this->translateFile) {
				return new Value\TranslatedFiles($this->owner, $this, $this->valueContext);
			}

			return new Value\Files($this->owner, $this, $this->valueContext);
		}

		if ($this->translateFile) {
			return new Value\TranslatedFile($this->owner, $this, $this->valueContext);
		}

		return new Value\File($this->owner, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		if ($this->translateFile) {
			return $this->getTranslatableFileStructure('file', $value);
		}

		return $this->getFileStructure('file', $value);
	}

	public function shape(): Shape
	{
		$limitValidators = $this->limitValidators();
		$shape = ValidationShape::create(title: $this->label, keepUnknown: true);
		$shape->add('type', 'text', 'required', 'in:file');

		if ($this->translateFile) {
			// File-translatable: separate file arrays per locale
			$subShape = ValidationShape::create(list: true, title: $this->label, keepUnknown: true);
			$subShape->add('file', 'text');
			$subShape->add('title', 'text');

			$i18nShape = ValidationShape::create(title: $this->label, keepUnknown: true);
			$locales = $this->owner->locales();

			foreach ($locales as $locale) {
				$i18nShape
					->add($locale->id, $subShape, ...$limitValidators)
					->prepare(ValidationShape::nullAsEmpty(...));
			}

			$shape
				->add('files', $i18nShape, ...$this->validators)
				->prepare(ValidationShape::nullAsEmpty(...));
		} elseif ($this->translate) {
			// Text-translatable: shared files but translatable titles
			$fileShape = ValidationShape::create(list: true, keepUnknown: true);
			$fileShape->add('file', 'text', 'required');

			$locales = $this->owner->locales();
			$titleShape = ValidationShape::create(title: $this->label, keepUnknown: true);

			foreach ($locales as $locale) {
				$titleShape->add($locale->id, 'text');
			}

			$fileShape->add('title', $titleShape)->prepare(ValidationShape::nullAsEmpty(...));
			$shape
				->add('files', $fileShape, ...$limitValidators, ...$this->validators)
				->prepare(ValidationShape::nullAsEmpty(...));
		} else {
			// Non-translatable
			$fileShape = ValidationShape::create(list: true, keepUnknown: true);
			$fileShape->add('file', 'text', 'required');
			$fileShape->add('title', 'text');
			$shape
				->add('files', $fileShape, ...$limitValidators, ...$this->validators)
				->prepare(ValidationShape::nullAsEmpty(...));
		}

		return $shape;
	}
}
