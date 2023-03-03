<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value;

class Option extends Field
{
    protected array $options = [];

    public function value(): Value\Option
    {
        return new Value\Option($this->node, $this, $this->valueContext);
    }

    public function add(string|array $option)
    {
        $this->options[] = $option;
    }
}
