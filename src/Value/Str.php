<?php

declare(strict_types=1);

namespace Conia\Core\Value;

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

    public function isset(): bool
    {
        return $this->raw() ? true : false;
    }
}
