<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Html as HtmlValue;
use Conia\Core\Value\ValueContext;

class Html extends Field
{
    public function value(Type $page, ValueContext $context): HtmlValue
    {
        return new HtmlValue($page, $this, $context);
    }
}
