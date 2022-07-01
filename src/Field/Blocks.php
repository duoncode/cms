<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Text;


class Blocks extends Field
{
    public function value(array $data, Locale $locale): Text
    {
        //TODO: use the correct value
        return new Text($data, $locale);
    }
}
