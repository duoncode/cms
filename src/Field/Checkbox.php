<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Boolean;


class Checkbox extends Field
{
    public function value(Request $request, array $data): Boolean
    {
        return new Boolean($request, $data);
    }
}
