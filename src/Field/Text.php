<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Text as TextValue;


class Text extends Field
{
    public function value(Request $request, array $data): TextValue
    {
        return new TextValue($request, $data);
    }
}
