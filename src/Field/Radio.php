<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Str;


class Radio extends Field
{
    public function value(array $data, Locale $locale): Str
    {
        return new Str($data, $locale);
    }
}
