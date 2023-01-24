<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Boolean;

class Checkbox extends Field
{
    public function value(Type $page, string $field, array $data): Boolean
    {
        return new Boolean($page, $field, $data);
    }
}
