<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Text;

class Blocks extends Field
{
    public function value(Type $page, string $field, array $data): Text
    {
        // TODO: use the correct value
        return new Text($page, $field, $data);
    }
}
