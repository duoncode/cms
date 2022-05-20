<?php

declare(strict_types=1);

namespace Conia\Field\Value;

use \RuntimeException;


trait TextValue
{
    protected string $value;
    protected string $default;

    public function getValue(): string
    {
        if (isset($this->value)) {
            return $this->value;
        }

        if (isset($this->default)) {
            return $this->default;
        }

        throw new RuntimeException('No value available');
    }

    public function setValue(string $value): void
    {
        $this->default = $value;
    }

    public function setDefault(string|array $value): void
    {
        $this->default = $value;
    }
}
