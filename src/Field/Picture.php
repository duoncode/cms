<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value;
use Conia\Core\Value\ValueContext;

class Picture extends Field
{
    protected bool $multiple = false;
    protected bool $translateImage = false;

    // TODO: translateImage and multiple
    public function value(Type $node, ValueContext $context): Value\Picture
    {
        return new Value\Picture($node, $this, $context);
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
