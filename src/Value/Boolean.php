<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Request;
use Conia\Core\Type;

class Boolean extends Value
{
    public readonly bool $value;

    public function __construct(Type $page, Request $request, array $data)
    {
        parent::__construct($page, $request, $data);

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
