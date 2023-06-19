<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value;

class Image extends Field
{
    protected bool $multiple = false;
    protected bool $translateImage = false;

    public function value(): Value\Images|Value\Image
    {
        if ($this->multiple) {
            if ($this->translateImage) {
                return new Value\TranslatedImages($this->node, $this, $this->valueContext);
            }

            return new Value\Images($this->node, $this, $this->valueContext);
        }

        if ($this->translateImage) {
            return new Value\TranslatedImage($this->node, $this, $this->valueContext);
        }

        return new Value\Image($this->node, $this, $this->valueContext);
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
        // Generate thumbs
        // TODO: add it to the api data. Currently we assume in the frontend that they are existing
        if ($this->multiple) {
            foreach ($this->value() as $image) {
                $image->width(400)->url();
            }
        } else {
            $value = $this->value();
            if ($value->count() > 0) {
                $value->width(400)->url();
            }
        }

        return array_merge(parent::asArray(), [
            'multiple' => $this->multiple,
            'translateImage' => $this->translateImage,
        ]);
    }
}
