<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Locale;
use Conia\Value\Decimal as DecimalValue;


class Decimal extends Field
{
    public function value(array $data, Locale $locale): DecimalValue
    {
        return new DecimalValue($data, $locale);
    }
}
