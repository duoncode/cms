<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Text as TextValue;

class Text extends Field
{
    public function value(): TextValue
    {
        return new TextValue($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getTranslatableStructure('text', $value);
    }
}
