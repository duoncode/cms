<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

class Block
{
    public function __construct(
        private readonly Context $context,
    ) {
    }
}
