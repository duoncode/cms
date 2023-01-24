<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Boolean;
use Conia\Core\Value\ValueContext;

class Checkbox extends Field
{
    public function value(Type $page, ValueContext $context): Boolean
    {
        return new Boolean($page, $context);
    }
}
