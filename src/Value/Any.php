<?php

declare(strict_types=1);

namespace Conia\Value;


class Any extends Value
{
    public function raw(): mixed
    {
        return $this->data['value'] ?? null;
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function json(): mixed
    {
        return $this->raw();
    }
}
