<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Any;


/**
 * A field type which is not shown in the admin
 */
class Hidden extends Field
{
    public function value(Request $request, array $data): Any
    {
        return new Any($request, $data);
    }
}
