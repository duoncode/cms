<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Attr;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Immutable {} // We can't use Readonly as it is a keyword of PHP
