<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\DateTime as DateTimeValue;

class DateTime extends Field
{
    public function value(): DateTimeValue
    {
        return new DateTimeValue($this->node, $this, $this->valueContext);
    }

    public function structure(): array
    {
        return $this->getSimpleStructure('datetime');
    }
}
