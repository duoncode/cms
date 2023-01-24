<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Options  as OptionsValue;
use Conia\Core\Value\ValueContext;

class Options extends Field
{
    public function value(Type $page, ValueContext $context): OptionsValue
    {
        return new OptionsValue($page, $context);
    }
}
