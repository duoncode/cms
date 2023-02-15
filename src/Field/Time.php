<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Time as TimeValue;
use Conia\Core\Value\ValueContext;

class Time extends Field
{
    public function value(Type $node, ValueContext $context): TimeValue
    {
        return new TimeValue($node, $this, $context);
    }
}
