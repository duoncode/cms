<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Field\Field;
use Conia\Block;


class Type
{
    protected string $name;
    protected string $template;

    public function __construct(
        public readonly string|array $label,
        protected array $permissions = [],
        ?string $name = null,
        ?string $template = null,
    ) {
        $class = basename(str_replace('\\', '/', strtolower($this::class)));
        $this->name = $name ?: $class;
        $this->template = $template ?:  $class . '.php';
    }

    public function get(): Generator
    {
        foreach ($this as $field => $object) {
            if (is_subclass_of(Block::class, $object)) {
                (yield $field => $object);
            } else {
                if (is_subclass_of(Field::class, $object)) {
                    (yield $field => $object);
                }
            }
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function template(): string
    {
        return $this->template;
    }
}
