<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Core\Value\Any;

/**
 * A field type which is not shown in the admin.
 */
class Hidden extends Field
{
    public function value(Request $request, array $data): Any
    {
        return new Any($request, $data);
    }
}
