<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Field\Field;
use Conia\Core\Type;

class Boolean extends Value
{
    public readonly bool $value;

    public function __construct(Type $page, Field $field, ValueContext $context)
    {
        parent::__construct($page, $field, $context);

        if (is_bool($this->data['value'] ?? null)) {
            $this->value = $this->data['value'];
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
