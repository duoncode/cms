<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Decimal as DecimalValue;

class Decimal extends Field
{
    public function value(Type $page, Request $request, array $data): DecimalValue
    {
        return new DecimalValue($page, $request, $data);
    }
}
