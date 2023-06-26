<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value;

class Picture extends Field
{
    protected bool $multiple = false;
    protected bool $translateFile = false;

    // TODO: translateFile and multiple
    public function value(): Value\Picture
    {
        return new Value\Picture($this->node, $this, $this->valueContext);
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

    public function asArray(): array
    {
        $value = $this->value();
        $count = $value->count();

        // Generate thumbs
        // TODO: add it to the api data. Currently we assume in the frontend that they are existing
        for ($i = 0; $i < $count; $i++) {
            $value->width(400)->url(false, $i);
        }

        return array_merge(parent::asArray(), [
            'multiple' => $this->multiple,
            'translateFile' => $this->translateFile,
        ]);
    }

    public function structure(): array
    {
        return $this->getFileStructure('picture');
    }
}
