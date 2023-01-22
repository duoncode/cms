<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Str;

class Radio extends Field
{
    public function value(Request $request, array $data): Str
    {
        return new Str($request, $data);
    }
}
