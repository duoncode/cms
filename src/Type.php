<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Data;


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
    }

    public function get(): Generator
    {
        foreach ($this as $field => $object) {
            if ($object instanceof Data) {
                (yield $field => $object->meta());
            }
        }
    }

    abstract public function init(): void;
    abstract public function title(): string;

    public function structure(): array
    {
        $result = [];

        foreach ($this->get() as $key => $value) {
            $result[] = array_merge(['name' => $key], $value);
        }

        return $result;
    }
}
