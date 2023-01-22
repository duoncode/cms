<?php

declare(strict_types=1);

namespace Conia\Core\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Label
{
    public function __construct(public readonly string $label)
    {
    }
}
