<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Field\Field;
use Conia\Core\Type;

class Number extends Value
{
    public readonly ?int $value;

    public function __construct(Type $page, Field $field, ValueContext $context)
    {
        parent::__construct($page, $field, $context);

        if (is_numeric($this->data['value'] ?? null)) {
            $this->value = (int)$this->data['value'];
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
