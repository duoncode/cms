<?php

declare(strict_types=1);

namespace Conia\Value;

class Str extends Value
{
    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function raw(): string
    {
        return $this->data['value'] ?? '';
    }

    public function json(): string
    {
        return $this->raw();
    }
}
