<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Value;


class Number extends Value
{
    public readonly ?int $value;

    public function __construct(
        array $data,
        string $locale
    ) {
        parent::__construct($data, $locale);

        if (is_numeric($data['value'] ?? null)) {
            $this->value = (int)$data['value'];
        } else {
            $this->value = null;
        }
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return '';
        }

        return (string)$this->value;
    }
}
