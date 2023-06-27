<?php

declare(strict_types=1);

namespace Conia\Core\Field;

class Textarea extends Text
{
    public function structure(mixed $value = null): array
    {
        return $this->getTranslatableStructure('textarea', $value);
    }
}
