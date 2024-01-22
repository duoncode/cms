<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Value\Html;

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
