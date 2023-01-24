<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Number as NumberValue;
use Conia\Core\Value\ValueContext;

class Number extends Field
{
    public function value(Type $page, ValueContext $context): NumberValue
    {
        return new NumberValue($page, $context);
    }
}
