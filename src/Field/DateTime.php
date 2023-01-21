<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\DateTime as DateTimeValue;

class DateTime extends Field
{
    public function value(Request $request, array $data): DateTimeValue
    {
        return new DateTimeValue($request, $data);
    }
}
