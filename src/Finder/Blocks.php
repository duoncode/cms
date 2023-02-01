<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

class Blocks
{
    public function __construct(
        private readonly Context $context,
    ) {
    }
}
