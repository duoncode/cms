<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Any;
use Conia\Core\Value\ValueContext;

/**
 * A field type which is not shown in the admin.
 */
class Hidden extends Field
{
    public function value(Type $node, ValueContext $context): Any
    {
        return new Any($node, $this, $context);
    }
}
