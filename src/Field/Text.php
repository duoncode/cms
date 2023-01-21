<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Text as TextValue;

class Text extends Field
{
    public function value(Request $request, array $data): TextValue
    {
        return new TextValue($request, $data);
    }
}
