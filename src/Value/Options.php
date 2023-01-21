<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Options extends Value
{
    public function __toString(): string
    {
        return 'Options: count(' . count($this->raw()) . ')';
    }

    public function raw(): array
    {
        return [];
    }

    public function json(): array
    {
        return $this->raw();
    }
}
