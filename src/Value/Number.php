<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Chuck\Request;
use Conia\Core\Type;

class Number extends Value
{
    public readonly ?int $value;

    public function __construct(Type $page, Request $request, array $data)
    {
        parent::__construct($page, $request, $data);

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

    public function json(): mixed
    {
        return $this->value;
    }
}
