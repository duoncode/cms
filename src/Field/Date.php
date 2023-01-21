<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Date as DateValue;

class Date extends Field
{
    public function value(Request $request, array $data): DateValue
    {
        return new DateValue($request, $data);
    }
}
