<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Value\Boolean;

class Checkbox extends Field
{
    public function value(): Boolean
    {
        return new Boolean($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('checkbox', $value);
    }
}
