<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Text;

class Blocks extends Field
{
    public function value(Request $request, array $data): Text
    {
        // TODO: use the correct value
        return new Text($request, $data);
    }
}
