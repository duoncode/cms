<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Text;
use Conia\Core\Value\ValueContext;

class Blocks extends Field
{
    public function value(Type $node, ValueContext $context): Text
    {
        return new Text($node, $this, $context);
    }
}
