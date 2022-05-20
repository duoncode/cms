<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Field;

abstract class Block implements Data
{
    protected array $list = [];
    protected array $fields = [];

    public function __construct(
        public readonly bool $repeatable = false,
    ) {
    }

    abstract public function init(): void;

    public function get(): Generator
    {
        foreach ($this as $field => $object) {
            if ($object instanceof Field) {
                (yield $field => $object->meta());
            }
        }
    }

    public function structure(): array
    {
        $result = [];

        foreach ($this->get() as $key => $value) {
            $result[] = array_merge(['name' => $key], $value);
        }

        return $result;
    }

    public function meta(): array
    {
        return [
            'type' => 'block',
            'label' => $this->label,
            'repeatable' => $this->repeatable,
            'description' => $this->description,
            'structure' => $this->structure(),
        ];
    }

    public function render(): string
    {
        return '';
    }
}
