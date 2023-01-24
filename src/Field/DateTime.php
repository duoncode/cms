<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\DateTime as DateTimeValue;
use Conia\Core\Value\ValueContext;

class DateTime extends Field
{
    public function value(Type $page, ValueContext $context): DateTimeValue
    {
        return new DateTimeValue($page, $this, $context);
    }
}
