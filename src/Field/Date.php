<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Date as DateValue;
use Conia\Core\Value\ValueContext;

class Date extends Field
{
    public function value(Type $node, ValueContext $context): DateValue
    {
        return new DateValue($node, $this, $context);
    }
}
