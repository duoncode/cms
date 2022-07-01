<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Locale;


class Images extends Files
{
    public function __toString(): string
    {
        return 'Images: count(' . count($this->raw()) . ')';
    }

    protected function getFile(array $file, Locale $locale): Image
    {
        return new Image($file, $locale);
    }
}
