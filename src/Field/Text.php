<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Text as TextValue;
use Conia\Core\Value\ValueContext;

class Text extends Field
{
    public function value(Type $node, ValueContext $context): TextValue
    {
        return new TextValue($node, $this, $context);
    }
}
