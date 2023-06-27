<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value;

class Option extends Field
{
    protected array $options = [];
    protected bool $hasLabel = false;

    public function value(): Value\Option
    {
        return new Value\Option($this->node, $this, $this->valueContext);
    }

    public function add(string|array $option): void
    {
        $this->options[] = $option;
    }

    public function options(array $options): void
    {
        if (is_array($options[0])) {
            $this->hasLabel = true;
        }

        $this->options = $options;
    }

    public function asArray(): array
    {
        $result = parent::asArray();
        $result['options'] = $this->options;
        $result['hasLabel'] = $this->hasLabel;

        return $result;
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('option', $value);
    }
}
