<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Value\Options  as OptionsValue;

class Options extends Field
{
    public function value(): OptionsValue
    {
        return new OptionsValue($this->node, $this, $this->valueContext);
    }
}
