<?php

declare(strict_types=1);

namespace Conia\Value;


class Str extends Value
{
    public function raw(): string
    {
        return $this->data['value'] ?? '';
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function json(): string
    {
        return $this->raw();
    }
}
