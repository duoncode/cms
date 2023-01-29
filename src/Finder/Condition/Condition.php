<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Finder\CompilesJsonAccessor;

abstract readonly class Condition
{
    use CompilesJsonAccessor;

    abstract public function print(): string;
}
