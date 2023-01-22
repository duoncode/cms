<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Type;
use Conia\Core\Value\Boolean;

class Checkbox extends Field
{
    public function value(Type $page, Request $request, array $data): Boolean
    {
        return new Boolean($page, $request, $data);
    }
}
