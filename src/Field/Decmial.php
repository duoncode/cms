<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Decimal as DecimalValue;
use Conia\Core\Value\ValueContext;

class Decimal extends Field
{
    public function value(Type $node, ValueContext $context): DecimalValue
    {
        return new DecimalValue($node, $this, $context);
    }
}
