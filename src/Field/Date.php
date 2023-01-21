<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Date as DateValue;

class Date extends Field
{
    public function value(Request $request, array $data): DateValue
    {
        return new DateValue($request, $data);
    }
}
