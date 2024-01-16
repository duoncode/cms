<?php

declare(strict_types=1);

namespace Conia\Cms\Value;

class ValueContext
{
    public function __construct(
        public readonly string $fieldName,
        public readonly array $data
    ) {
    }
}
