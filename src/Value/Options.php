<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Options extends Value
{
    public function __toString(): string
    {
        return 'Options: count(' . count($this->unwrap()) . ')';
    }

    public function unwrap(): array
    {
        return [];
    }

    public function json(): array
    {
        return $this->unwrap();
    }

    public function isset(): bool
    {
        return count($this->unwrap()) > 0 ? true : false;
    }
}
