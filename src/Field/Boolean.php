<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field;


class Boolean extends Field
{
    public function is(bool $value)
    {
        return $this->value === $value;
    }

    public function __toString(): string
    {
        return '';
    }
}
