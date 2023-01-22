<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Number as NumberValue;

class Number extends Field
{
    public function value(Type $page, Request $request, array $data): NumberValue
    {
        return new NumberValue($page, $request, $data);
    }
}
