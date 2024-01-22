<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Field\Field;
use Conia\Cms\Value;

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

    public function properties(): array
    {
        $result = parent::properties();
        $result['options'] = $this->options;
        $result['hasLabel'] = $this->hasLabel;

        return $result;
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('option', $value);
    }
}
