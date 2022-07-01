<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Boolean;


class Checkbox extends Field
{
    public function value(array $data, Locale $locale): Boolean
    {
        return new Boolean($data, $locale);
    }
}
