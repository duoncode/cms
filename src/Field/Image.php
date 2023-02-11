<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Image as SingleImage;
use Conia\Core\Value\Images;
use Conia\Core\Value\ValueContext;

class Image extends Field
{
    protected bool $multiple = false;
    protected bool $translateImage = false;

    public function value(Type $page, ValueContext $context): Images|SingleImage
    {
        if ($this->multiple) {
            return new Images($page, $this, $context);
        }

        return new SingleImage($page, $this, $context);
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

     public function translateImage(bool $translate = true): static
     {
         $this->translateImage = $translate;

         return $this;
     }

     public function shouldTranslateImage(): bool
     {
         return $this->translateImage;
     }
}
