<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Text;

class Blocks extends Field
{
    public function value(): Text
    {
        return new Text($this->page, $this, $this->valueContext);
    }
}
