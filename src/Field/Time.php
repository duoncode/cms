<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Type;
use Conia\Core\Value\Time as TimeValue;

class Time extends Field
{
    public function value(Type $page, array $data): TimeValue
    {
        return new TimeValue($page, $data);
    }
}
