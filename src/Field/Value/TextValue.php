<?php

declare(strict_types=1);

namespace Conia\Field;


trait TextValue
{
    protected string $value;

    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
