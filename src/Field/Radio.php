<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Str;
use Conia\Core\Value\ValueContext;

class Radio extends Field
{
    public function value(Type $page, ValueContext $context): Str
    {
        return new Str($page, $context);
    }
}
