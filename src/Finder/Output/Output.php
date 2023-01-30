<?php

declare(strict_types=1);

namespace Conia\Core\Finder\Output;

interface Output
{
    public function get(): string;
}
