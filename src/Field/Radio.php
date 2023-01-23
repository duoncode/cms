<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Str;

class Radio extends Field
{
    public function value(Type $page, array $data): Str
    {
        return new Str($page, $data);
    }
}
