<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Date as DateValue;

class Date extends Field
{
    public function value(): DateValue
    {
        return new DateValue($this->node, $this, $this->valueContext);
    }
}
