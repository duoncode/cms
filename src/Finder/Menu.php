<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

class Menu
{
    public function __construct(
        private readonly Context $context,
    ) {
    }
}
