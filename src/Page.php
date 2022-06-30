<?php

declare(strict_types=1);

namespace Conia;


class Page
{
    public function __construct(
        protected readonly Type $type,
        protected readonly array $data
    ) {
    }
}
