<?php

declare(strict_types=1);

namespace Conia\Core\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Columns
{
    public function __construct(
        public readonly int $columns,
        public readonly int $minCellWidth = 1,
    ) {
    }
}
