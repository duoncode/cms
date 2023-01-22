<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Conia\Core\Value\Html;

class Iframe extends Field
{
    public function value(Type $page, Request $request, array $data): Html
    {
        return new Html($page, $request, $data);
    }
}
