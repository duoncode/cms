<?php

declare(strict_types=1);

namespace Conia;


class Layout
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array $columns = []
    ) {
    }
}
