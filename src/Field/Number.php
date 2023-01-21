<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Request;
use Conia\Value\Number as NumberValue;

class Number extends Field
{
    public function value(Request $request, array $data): NumberValue
    {
        return new NumberValue($request, $data);
    }
}
