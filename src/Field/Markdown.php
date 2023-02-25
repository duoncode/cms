<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Html;

class Markdown extends Field
{
    public function value(): Html
    {
        return new Html($this->node, $this, $this->valueContext);
    }
}
