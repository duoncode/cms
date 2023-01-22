<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class GridItem
{
    public function __construct(
        public readonly string $type,
        public readonly array $data
    )
    {
    }
}
