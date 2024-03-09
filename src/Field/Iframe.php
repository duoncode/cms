<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

class Iframe extends Text
{
    public function structure(mixed $value = null): array
    {
        return $this->getTranslatableStructure('iframe', $value);
    }
}
