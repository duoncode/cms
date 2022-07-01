<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Time as TimeValue;


class Time extends Field
{
    public function value(array $data, Locale $locale): TimeValue
    {
        return new TimeValue($data, $locale);
    }
}
