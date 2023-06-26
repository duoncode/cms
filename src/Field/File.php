<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value;

class File extends Field
{
    protected bool $multiple = false;
    protected bool $translateFile = false;

    public function value(): Value\File|Value\Files
    {
        if ($this->multiple) {
            return new Value\Files($this->node, $this, $this->valueContext);
        }

        return new Value\File($this->node, $this, $this->valueContext);
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function translateFile(bool $translate = true): static
    {
        $this->translateFile = $translate;
        $this->translate = $translate;

        return $this;
    }

    public function structure(): array
    {
        return $this->getFileStructure('file');
    }

    public function asArray(): array
    {
        return array_merge(parent::asArray(), [
            'multiple' => $this->multiple,
            'translateFile' => $this->translateFile,
        ]);
    }
}
