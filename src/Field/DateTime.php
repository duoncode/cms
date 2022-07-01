<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\DateTime as DateTimeValue;


class DateTime extends Field
{
    public function value(array $data, Locale $locale): DateTimeValue
    {
        return new DateTimeValue($data, $locale);
    }
}
