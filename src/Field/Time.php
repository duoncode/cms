<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Time as TimeValue;

class Time extends Field
{
    public function value(Request $request, array $data): TimeValue
    {
        return new TimeValue($request, $data);
    }
}
