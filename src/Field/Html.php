<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Html as HtmlValue;

class Html extends Field
{
    public function value(Type $page, string $field, array $data): HtmlValue
    {
        return new HtmlValue($page, $field, $data);
    }
}
