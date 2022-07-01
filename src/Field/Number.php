<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Locale;
use Conia\Value\Number as NumberValue;


class Number extends Field
{
    public function value(array $data, Locale $locale): NumberValue
    {
        return new NumberValue($data, $locale);
    }
}
