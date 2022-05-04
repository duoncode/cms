<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use Conia\Field\Field;
use Conia\Block;


class Template
{
    public function __construct(
        public readonly string|array $label,
        protected array $permissions = [],
    ) {
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
}
