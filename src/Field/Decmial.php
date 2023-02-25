<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value\Decimal as DecimalValue;

class Decimal extends Field
{
    public function value(): DecimalValue
    {
        return new DecimalValue($this->node, $this, $this->valueContext);
    }
}
