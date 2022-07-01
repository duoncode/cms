<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Text as TextValue;


class Text extends Field
{
    public function value(array $data, Locale $locale): TextValue
    {
        return new TextValue($data, $locale);
    }
}
