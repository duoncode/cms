<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Html;

class Iframe extends Field
{
    public function value(): Html
    {
        return new Html($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getTranslatableStructure('iframe', $value);
    }
}
