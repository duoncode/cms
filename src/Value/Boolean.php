<?php

declare(strict_types=1);

namespace Conia\Value;


use Conia\Locale;

class Boolean extends Value
{
    public readonly bool $value;

    public function __construct(
        array $data,
        Locale $locale
    ) {
        parent::__construct($data, $locale);

        if (is_bool($data['value'] ?? null)) {
            $this->value = $data['value'];
        } else {
            $this->value = false;
        }
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function json(): mixed
    {
        return $this->value;
    }
}
