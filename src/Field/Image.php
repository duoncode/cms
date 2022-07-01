<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Locale;
use Conia\Value\Images;


class Image extends Field
{
    public function value(array $data, Locale $locale): Images
    {
        return new Images($data, $locale);
    }
}
