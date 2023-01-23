<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Date as DateValue;

class Date extends Field
{
    public function value(Type $page, array $data): DateValue
    {
        return new DateValue($page, $data);
    }
}
