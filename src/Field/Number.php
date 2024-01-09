<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value\Number as NumberValue;

class Number extends Field
{
    public function value(): NumberValue
    {
        return new NumberValue($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('number', $value);
    }
}
