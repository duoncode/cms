<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Html;

class Iframe extends Field
{
    public function value(Type $page, string $field, array $data): Html
    {
        return new Html($page, $field, $data);
    }
}
