<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Str;


class Radio extends Field
{
    public function value(Request $request, array $data): Str
    {
        return new Str($request, $data);
    }
}
