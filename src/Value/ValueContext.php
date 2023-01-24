<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Field\Field;

class ValueContext
{
    public function __construct(
        public readonly Field $field,
        public readonly string $fieldName,
        public readonly array $data
    ) {
    }
}
