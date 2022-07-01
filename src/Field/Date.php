<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Date as DateValue;


class Date extends Field
{
    public function value(array $data, Locale $locale): DateValue
    {
        return new DateValue($data, $locale);
    }
}
