<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Any extends Value
{
    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function raw(): mixed
    {
        return $this->data['value'] ?? null;
    }

    public function json(): mixed
    {
        return $this->raw();
    }

    public function isset(): bool
    {
        return isset($this->data['value']) ? true : false;
    }
}
