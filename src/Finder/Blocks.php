<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Context;
use Conia\Core\Finder;

class Blocks
{
    public function __construct(
        private readonly Context $context,
        private readonly Finder $find,
    ) {
    }
}
