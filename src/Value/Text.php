<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Value;


class Text extends Value
{
    public function __toString(): string
    {
        return htmlspecialchars($this->data[$this->locale]);
    }

    public function raw(): string
    {
        return $this->data[$this->locale];
    }
}
