<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value;
use Conia\Core\Value\ValueContext;

class Image extends Field
{
    protected bool $multiple = false;
    protected bool $translateImage = false;

    public function value(Type $page, ValueContext $context): Value\Images|Value\Image
    {
        if ($this->multiple) {
            if ($this->translateImage) {
                return new Value\TranslatedImages($page, $this, $context);
            }

            return new Value\Images($page, $this, $context);
        }

        if ($this->translateImage) {
            return new Value\TranslatedImage($page, $this, $context);
        }

        return new Value\Image($page, $this, $context);
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
}
