<?php

declare(strict_types=1);

namespace Conia\Core;

class RememberDetails
{
    public function __construct(
        public readonly Token $token,
        public readonly int $expires
    ) {
    }
}
