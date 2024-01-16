<?php

declare(strict_types=1);

namespace Conia\Cms\Finder\Output;

interface Output
{
    public function get(): string;
}
