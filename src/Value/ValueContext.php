<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class ValueContext
{
    public function __construct(
        public readonly string $fieldName,
        public readonly array $data
    ) {
    }
}
