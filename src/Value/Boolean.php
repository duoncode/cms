<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Request;

class Boolean extends Value
{
    public readonly bool $value;

    public function __construct(Request $request, array $data)
    {
        parent::__construct($request, $data);

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
