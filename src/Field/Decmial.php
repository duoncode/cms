<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Field\Field;
use Conia\Cms\Value\Decimal as DecimalValue;

class Decimal extends Field
{
    public function value(): DecimalValue
    {
        return new DecimalValue($this->node, $this, $this->valueContext);
    }

    public function structure(mixed $value = null): array
    {
        return $this->getSimpleStructure('decimal', $value);
    }
}
