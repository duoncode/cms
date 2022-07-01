<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Locale;
use Conia\Value\Files;


class File extends Field
{
    public function value(array $data, Locale $locale): Files
    {
        return new Files($data, $locale);
    }
}
