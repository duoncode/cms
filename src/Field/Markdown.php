<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Value\Html;

class Markdown extends Field
{
    public function value(Request $request, array $data): Html
    {
        return new Html($request, $data);
    }
}
