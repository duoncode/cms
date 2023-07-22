<?php

declare(strict_types=1);

namespace Conia\Core;

class Section
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
