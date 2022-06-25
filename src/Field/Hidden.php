<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Field;


/**
 * A field type which is not shown in the admin
 */
class Hidden extends Field
{

    public function __toString(): string
    {
        return '';
    }
}
