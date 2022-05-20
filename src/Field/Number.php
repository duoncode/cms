<?php

declare(strict_types=1);

namespace Conia\Field;

use \RuntimeException;
use Conia\Field;


class Number extends Field
{
    protected int|float $value;
    protected int|float $default;

    public function __toString()
    {
        return '';
    }

    public function value(): int|float
    {
        if (isset($this->value)) {
            return $this->value;
        }

        if (isset($this->default)) {
            return $this->default;
        }

        throw new RuntimeException('No value available');
    }

    public function setValue(int|float $value): void
    {
        $this->default = $value;
    }

    public function setDefault(int|float|array $value): void
    {
        $this->default = $value;
    }
}
