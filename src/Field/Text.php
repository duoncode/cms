<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Text as TextValue;

class Text extends Field
{
    public function value(Type $page, array $data): TextValue
    {
        return new TextValue($page, $data);
    }
}
