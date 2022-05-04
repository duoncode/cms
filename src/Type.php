<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Field\Field;
use Conia\Block;


abstract class Type
{
    public readonly string $name;
    public readonly string $template;

    public function __construct(
        public readonly string|array $label,
        public readonly array $permissions = [],
        ?string $name = null,
        ?string $template = null,
    ) {
        $class = basename(str_replace('\\', '/', strtolower($this::class)));
        $this->name = $name ?: $class;
        $this->template = $template ?:  $class . '.php';

        $this->init();
    }

    public function get(): Generator
    {
        foreach ($this as $field => $object) {
            if (is_subclass_of($object, Block::class)) {
                (yield $field => ['type' => 'block', 'value' => $object]);
            } else {
                if (is_subclass_of($object, Field::class)) {
                    (yield $field => ['type' => $object->type, 'value' => $object]);
                }
            }
        }
    }

    protected function init(): void
    {
    }

    public function structure(): array
    {
        $result = [];

        foreach ($this->get() as $key => $value) {
            $result[] = [
                'name' => $key,
                'type' => $value['type'],
            ];
        }

        return $result;
    }
}
