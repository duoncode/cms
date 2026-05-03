<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Validation\Shape as ValidationShape;
use Duon\Cms\Value;
use Duon\Sire\Shape;

class Video extends Field implements
	Capability\Limitable,
	Capability\File\Translatable,
	Capability\Translatable
{
	use Capability\IsLimitable;
	use Capability\File\IsTranslatable;
	use Capability\IsTranslatable;

	public function value(): Value\Video
	{
		if ($this->translateFile) {
			return new Value\Video($this->owner, $this, $this->valueContext);
		}

		return new Value\Video($this->owner, $this, $this->valueContext);
	}

	public function structure(mixed $value = null): array
	{
		if ($this->translateFile) {
			return $this->getTranslatableFileStructure('video', $value);
		}

		return $this->getFileStructure('video', $value);
	}

	public function shape(): Shape
	{
		$limitValidators = $this->limitValidators();
		$shape = ValidationShape::create(title: $this->label, keepUnknown: true);
		$shape->add('type', 'text', 'required', 'in:video');

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
			$defaultLocale = $locales->getDefault()->id;
			$titleShape = ValidationShape::create(title: $this->label, keepUnknown: true);

			foreach ($locales as $locale) {
				$localeValidators = [];

				if ($this->isRequired() && $locale->id === $defaultLocale) {
					$localeValidators[] = 'required';
				}

				$titleShape->add($locale->id, 'text', ...$localeValidators);
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
