<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Field\Field;


abstract class Block implements Data
{
    public function __construct(
        protected readonly ?string $label = null,
        protected readonly bool $repeatable = false,
        protected readonly ?string $description = null,
        protected readonly ?string $template = null,
    ) {
        $this->init();
    }

    protected function init(): void
    {
    }

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
