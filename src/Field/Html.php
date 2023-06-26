<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Html as HtmlValue;

class Html extends Field
{
    public function value(): HtmlValue
    {
        return new HtmlValue($this->node, $this, $this->valueContext);
    }

    public function structure(): array
    {
        return $this->getTranslatableStructure('html');
    }
}
