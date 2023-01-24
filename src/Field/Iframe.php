<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Html;
use Conia\Core\Value\ValueContext;

class Iframe extends Field
{
    public function value(Type $page, ValueContext $context): Html
    {
        return new Html($page, $context);
    }
}
