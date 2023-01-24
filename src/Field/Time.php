<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Time as TimeValue;
use Conia\Core\Value\ValueContext;

class Time extends Field
{
    public function value(Type $page, ValueContext $context): TimeValue
    {
        return new TimeValue($page, $this, $context);
    }
}
