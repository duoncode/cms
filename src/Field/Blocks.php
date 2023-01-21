<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Text;

class Blocks extends Field
{
    public function value(Request $request, array $data): Text
    {
        // TODO: use the correct value
        return new Text($request, $data);
    }
}
