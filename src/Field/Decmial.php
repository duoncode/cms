<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Request;
use Conia\Value\Decimal as DecimalValue;


class Decimal extends Field
{
    public function value(Request $request, array $data): DecimalValue
    {
        return new DecimalValue($request, $data);
    }
}
