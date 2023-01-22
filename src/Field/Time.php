<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Conia\Core\Value\Time as TimeValue;

class Time extends Field
{
    public function value(Type $page, Request $request, array $data): TimeValue
    {
        return new TimeValue($page, $request, $data);
    }
}
