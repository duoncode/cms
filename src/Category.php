<?php

declare(strict_types=1);

namespace Conia\Core;

class Category
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly array $categories,
    ) {
    }
}
