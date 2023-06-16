<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value;

class Picture extends Field
{
    protected bool $multiple = false;
    protected bool $translateImage = false;

    // TODO: translateImage and multiple
    public function value(): Value\Picture
    {
        return new Value\Picture($this->node, $this, $this->valueContext);
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

     public function translateImage(bool $translate = true): static
     {
         $this->translateImage = $translate;
         $this->translate = $translate;

         return $this;
     }

    public function asArray(): array
    {
        return array_merge(parent::asArray(), [
            'multiple' => $this->multiple,
            'translateImage' => $this->translateImage,
        ]);
    }
}
