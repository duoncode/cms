<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Decimal as DecimalValue;

class Decimal extends Field
{
    public function value(Type $page, array $data): DecimalValue
    {
        return new DecimalValue($page, $data);
    }
}
