<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Html;


class Iframe extends Field
{
    public function value(array $data, Locale $locale): Html
    {
        return new Html($data, $locale);
    }
}
