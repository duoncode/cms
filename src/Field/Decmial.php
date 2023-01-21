<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Core\Field\Field;
use Conia\Core\Value\Decimal as DecimalValue;

class Decimal extends Field
{
    public function value(Request $request, array $data): DecimalValue
    {
        return new DecimalValue($request, $data);
    }
}
