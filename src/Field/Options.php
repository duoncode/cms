<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Options  as OptionsValue;

class Options extends Field
{
    public function value(Type $page, string $field, array $data): OptionsValue
    {
        return new OptionsValue($page, $field, $data);
    }
}
