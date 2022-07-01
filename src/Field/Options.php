<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field\Field;
use Conia\Locale;
use Conia\Value\Options as OptionsValue;


class Options extends Field
{
    public function value(array $data, Locale $locale): OptionsValue
    {
        return new OptionsValue($data, $locale);
    }
}
