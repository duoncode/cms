<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Value\Any;

/**
 * A field type which is not shown in the admin.
 */
class Hidden extends Field
{
    public function value(): Any
    {
        return new Any($this->node, $this, $this->valueContext);
    }
}
