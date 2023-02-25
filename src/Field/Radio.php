<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Str;

class Radio extends Field
{
    public function value(): Str
    {
        return new Str($this->node, $this, $this->valueContext);
    }
}
