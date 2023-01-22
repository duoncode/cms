<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Conia\Core\Value\DateTime as DateTimeValue;

class DateTime extends Field
{
    public function value(Type $page, Request $request, array $data): DateTimeValue
    {
        return new DateTimeValue($page, $request, $data);
    }
}
