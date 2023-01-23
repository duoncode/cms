<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Html;

class Markdown extends Field
{
    public function value(Type $page, array $data): Html
    {
        return new Html($page, $data);
    }
}
