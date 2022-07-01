<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Html;


class Wysiwyg extends Field
{
    public function value(Request $request, array $data): Html
    {
        return new Html($request, $data);
    }
}
