<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Field\Field;
use Conia\Cms\Value;

class Video extends Field
{
    protected bool $multiple = false;
    protected bool $translateFile = false;

    public function value(): Value\Video
    {
        if ($this->translateFile) {
            return new Value\Video($this->node, $this, $this->valueContext);
        }

        return new Value\Video($this->node, $this, $this->valueContext);
    }

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

    public function properties(): array
    {
        return array_merge(parent::properties(), [
            'translateFile' => $this->translateFile,
        ]);
    }

    public function structure(mixed $value = null): array
    {
        if ($this->translateFile) {
            return $this->getTranslatableFileStructure('video', $value);
        }

        return $this->getFileStructure('video', $value);
    }
}
