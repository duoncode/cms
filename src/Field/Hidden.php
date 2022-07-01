<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Any;


/**
 * A field type which is not shown in the admin
 */
class Hidden extends Field
{
    public function value(array $data, Locale $locale): Any
    {
        return new Any($data, $locale);
    }
}
