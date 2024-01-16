<?php

declare(strict_types=1);

namespace Conia\Cms\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Fulltext
{
    public function __construct(public readonly FulltextWeight $fulltextWeight)
    {
    }
}
