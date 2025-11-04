<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

trait FileIsTranslatable
{
	protected bool $translateFile = false;

	public function translateFile(bool $translate = true): static
	{
		$this->translateFile = $translate;
		$this->translate = $translate;

		return $this;
	}

	public function isFileTranslatable(): bool
	{
		return $this->translateFile;
	}
}
