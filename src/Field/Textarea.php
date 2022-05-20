<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field;
use Conia\Field\Value\TextValue;


class Textarea extends Field
{
    use TextValue;

    public function __toString(): string
    {
        return '';
    }
}
