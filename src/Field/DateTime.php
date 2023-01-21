<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\DateTime as DateTimeValue;

class DateTime extends Field
{
    public function value(Request $request, array $data): DateTimeValue
    {
        return new DateTimeValue($request, $data);
    }
}
