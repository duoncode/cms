<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Chuck\Request;
use Conia\Core\Field\Field;
use Conia\Core\Type;
use Conia\Core\Value\Options  as OptionsValue;

class Options extends Field
{
    public function value(Type $page, Request $request, array $data): OptionsValue
    {
        return new OptionsValue($page, $request, $data);
    }
}
